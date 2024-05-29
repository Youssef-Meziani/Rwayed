<?php

namespace App\Controller;

use App\Coupon\CouponInterface;
use App\Events\CommandeEvent;
use App\Events\PneuStockEvent;
use App\EventSubscriber\PneuStockListener;
use App\Form\BillingDetailsType;
use App\Form\CouponType;
use App\Order\Factory\PanierFactory;
use App\Order\Storage\OrderStorageInterface;
use App\Processor\CartProcessor;
use App\Repository\AdresseRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CheckoutController extends AbstractController
{
    public function __construct(
        private CouponInterface $couponManager,
        private PanierFactory $panierFactory,
        private CartProcessor $cartProcessor,
        private OrderStorageInterface $orderStorage,
        private EventDispatcherInterface $eventDispatcher,
    ) { }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/checkout', name: 'checkout')]
    public function index(Request $request, AdresseRepository $adresseRepository): Response
    {
        $session = $request->getSession();
        $user = $this->getUser();
        if (!$user) {
            $session->set('referer_checkout', $request->getUri());
            return $this->redirectToRoute("app_login");
        }
        $commande = $this->panierFactory->create();

        $couponForm = $this->createForm(CouponType::class);
        $couponForm->handleRequest($request);

        if ($couponForm->isSubmitted() && $couponForm->isValid()) {
            $coupon = $couponForm->get('coupon_code')->getData();
            $panierCoupon = $this->couponManager->applyVoucher($coupon, $commande);
            $session->set('couponData', [
                'couponData' => $coupon,
                'reduction' => $panierCoupon->getTotal(),
                'pourcentage' => $panierCoupon->getDiscountRate(),
            ]);
        }

        $defaultAddress = $user ? $adresseRepository->findDefaultAddressByAdherent($user->getId()) : null;

        $billingForm = $this->createForm(BillingDetailsType::class, null, [
            'first_name' => $user?->getNom() ?? '',
            'last_name' => $user?->getPrenom() ?? '',
            'street_address' => $defaultAddress?->getStreet() ?? '',
            'city' => $defaultAddress?->getCity() ?? '',
            'postcode' => $defaultAddress?->getPostcode() ?? '',
            'email' => $user?->getEmail() ?? '',
            'phone' => $user?->getTele() ?? '',
        ]);
        $billingForm->handleRequest($request);

        if ($billingForm->isSubmitted() && $billingForm->isValid()) {
            $coupon = $session->get('couponData')['couponData'] ?? null;
            $this->cartProcessor->process($commande, $coupon);

            $eventCamera = new PneuStockEvent($commande->getLigneCommandes());
            $this->eventDispatcher->dispatch($eventCamera, PneuStockListener::NAME);

            $event = new CommandeEvent($commande);
            $this->eventDispatcher->dispatch($event, CommandeEvent::NAME);
            $this->couponManager->invalidateCoupon($session->get('voucher_code'));

            $subTotal = $this->orderStorage->prixTotalPanier();
            $prixTotal = $this->orderStorage->prixTotalPanier() + $this->orderStorage->totalTaxPanier();
            $totalTax = $this->orderStorage->totalTaxPanier();

            $session->remove('panier');
            $session->remove('coupon');
            $session->remove('couponData');

            $session->set('commande', $commande);
            $session->set('defaultAddress', $defaultAddress);

            return $this->redirectToRoute('order-success', [
                'totalSub' => $subTotal,
                'totalCredit' => $prixTotal,
                'tax' => $totalTax,
            ]);
        }

        return $this->render('checkout.twig', [
            'form_coupon' => $couponForm->createView(),
            'form_billign' => $billingForm->createView(),
            'coupon' => $session->get('couponData')['couponData'] ?? null,
            'total' => $commande->computeTotal(),
            'reduction' => $session->get('couponData')['reduction'] ?? null,
            'rate' => $session->get('couponData')['pourcentage'] ?? null,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    #[Route('/verify-coupon', name: 'verify_coupon')]
    public function verifyCoupon(Request $request): JsonResponse
    {
        $couponCode = $request->query->get('coupon_code');

        try {
            $coupon = $this->couponManager->findCouponByCode($couponCode);
            if ($coupon->getStatusAsString() !== 'Active') {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'The coupon you entered is currently inactive. Please check the code or use a different coupon.'
                ]);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'The coupon code you entered is valid and has been successfully applied to your order.'
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'The coupon code you entered does not exist. Please check the code and try again.'
            ]);
        }
    }
}
