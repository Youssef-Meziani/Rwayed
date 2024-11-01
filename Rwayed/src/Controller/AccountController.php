<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Adherent;
use App\Entity\Commande;
use App\Entity\Personne;
use App\Form\AddresseType;
use App\Form\ChangeCurrentPasswordType;
use App\Form\ProfileType;
use App\Repository\AdresseRepository;
use App\Repository\CommandeRepository;
use App\Services\AddressesService;
use App\Services\PasswordHasherService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_ADHERENT')]
class AccountController extends AbstractController
{

    private $entityManager;
    private PasswordHasherService $passwordHasherService;
    public function __construct(EntityManagerInterface $entityManager, PasswordHasherService $passwordHasherService)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasherService = $passwordHasherService;

    }

    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();
        $orders = $this->entityManager
            ->getRepository(Commande::class)
            ->findBy(['adherent' => $user], ['dateCommande' => 'DESC'], 3);

        return $this->render('account/dashboard.twig', [
            'user' => $user,
            'orders' => $orders
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/order-details/{codeUnique}', name: 'order_details')]
    public function orderDetails(string $codeUnique, CommandeRepository $commandeRepository, AdresseRepository $addressRepository): Response
    {
        $user = $this->getUser();
        $order = $commandeRepository->findOneBy([
            'codeUnique' => $codeUnique]);

        if (!$order || $order->getAdherent() !== $user) {
            throw $this->createNotFoundException('Order not found or access denied.');
        }
        $orderLines = $order->getLigneCommandes();

        $total = 0;
        foreach ($orderLines as $line) {
            $total += $line->getPrix() * $line->getQuantity();
        }

        $defaultAddress = $addressRepository->findDefaultAddressByAdherent($user);

        return $this->render('account/order-details.twig', [
            'order' => $order,
            'orderLines' => $orderLines,
            'total' => $total,
            'billingAddress' => $defaultAddress,
        ]);
    }


    #[Route('/addresses', name: 'addresses')]
    public function addresses(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si un utilisateur est connecté
        if (!$user) {
            throw $this->createAccessDeniedException('You must be logged in to access this page.');
        }
        /**@var User $user */
        // Récupérer les adresses de l'utilisateur connecté
        $addresses = $user->getAdresses();

        return $this->render('account/addresses.twig', [
            'addresses' => $addresses,
        ]);
    }
    #[Route('/address/add', name: 'address_add')]
    public function addAddress(Request $request, AddressesService $addressesService): Response
    {
        // Récupérer l'utilisateur connecté (l'adhérent)
        $adherent = $this->getUser();
    
        // Créer une nouvelle instance d'Adresse
        $adresse = new Adresse();
    
        // Créer le formulaire à partir du type de formulaire AdresseType
        $form = $this->createForm(AddresseType::class, $adresse);
    
        // Gérer la soumission du formulaire
        $form->handleRequest($request);
        
        //dd($request->request->all());
        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'adhérent à l'adresse
            $adresse->setAdherent($adherent);
    
            // Appeler la méthode addAddress du service AddressesService pour ajouter l'adresse
            $addressesService->addAddress($adresse,$adherent);
            $this->addFlash('success', 'Address successfully added.');
            //Check if the checkbox for setting as default address is checked
        if ($adresse->isSetasmydefaultaddress()) {
            $addressesService->setAsDefaultAddress($adresse, $adherent);
        }
           
            // Rediriger vers la page des adresses après l'ajout réussi
            return $this->redirectToRoute('addresses');
        }
        foreach ($form->getErrors(true) as $error) {
            $this->addFlash('error', $error->getMessage());
        }
        // Afficher le formulaire de création d'adresse
        return $this->render('account/add-address.twig', [
            'form' => $form->createView(), 
        ]);
    }
    

    #[Route('/address/edit/{id}', name: 'address_edit')]
    public function editAddress(Request $request, Adresse $adresse, AddressesService $addressesService): Response
    {
       // Créer le formulaire à partir du type de formulaire AdresseType et pré-remplir avec les données de l'adresse existante
       $form = $this->createForm(AddresseType::class, $adresse);
    
       // Gérer la soumission du formulaire
       $form->handleRequest($request);
    
       // Vérifier si le formulaire a été soumis et est valide
       if ($form->isSubmitted() && $form->isValid()) {

           // Appeler la méthode updateAddress du service AddressesService pour mettre à jour l'adresse
           $addressesService->updateAddress($adresse);
           $this->addFlash('success', 'Address successfully updated.');
            // Check if the checkbox for setting as default address is checked
            if ($adresse->isSetasmydefaultaddress()) {
                $addressesService->setAsDefaultAddress($adresse, $this->getUser());
            }
           // Rediriger vers la page des adresses après la mise à jour réussie
           return $this->redirectToRoute('addresses');
       }
       // Afficher le formulaire d'édition d'adresse
       return $this->render('account/edit-address.twig', [
           'form' => $form->createView(),
       ]);
    }
    

    #[Route('/address/delete/{id}', name: 'address_delete')]
    public function deleteAddress(Adresse $adresse, AddressesService $addressesService): Response
    {
         // Appeler la méthode deleteAddress du service AddressesService pour supprimer l'adresse
         $addressesService->deleteAddress($adresse);
           $this->addFlash('success', 'Address successfully deleted.');
         // Rediriger vers la page des adresses après la suppression réussie
         return $this->redirectToRoute('addresses');
    }


    // src/Controller/AccountController.php

    #[Route('/profile', name: 'profile')]
    public function profile(Request $request): Response
    {
        $user = $this->getUser();

        if (!$user instanceof Adherent) {
            throw $this->createNotFoundException('User not found');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Profile successfully updated.');

            return $this->redirectToRoute('profile');
        }

        return $this->render('account/profile.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/acount-orders', name: 'acount-orders')]
    public function acountOrders(): Response
    {
        $user = $this->getUser();
        $orders = $this->entityManager->getRepository(Commande::class)
            ->findByAdherent($user->getId());

        return $this->render('account/orders.twig', [
            'orders' => $orders
        ]);
    }

    #[Route('/change-password', name: 'change-password')]
    public function changePassword(Request $request): Response
    {
        /** @var Personne $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangeCurrentPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            
            // Check if the current password matches the user's actual password
            if ($this->passwordHasherService->isPasswordValid($user, $currentPassword)) {
                // Password change logic
                $newPassword = $form->get('plainPassword')->get('first')->getData();
                $this->passwordHasherService->setPassword($user, $newPassword);

                $this->addFlash('success', 'Password changed successfully.');
                return $this->redirectToRoute('change-password');
            }

            $this->addFlash('error', 'Current password is incorrect.');
        }

        return $this->render('account/password.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/garage', name: 'garage')]
    public function garage(AuthorizationCheckerInterface $authorizationChecker): Response
    {
        if (!$authorizationChecker->isGranted('ROLE_TECHNICIEN')) {
            throw new NotFoundHttpException();
        }

        return $this->render('account/garage.twig');
    }
}