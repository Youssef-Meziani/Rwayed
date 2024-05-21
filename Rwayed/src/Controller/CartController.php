<?php

namespace App\Controller;

use App\Coupon\CouponInterface;
use App\Form\CouponType;
use App\Order\Factory\PanierFactory;
use App\Processor\CartProcessor;
use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuStrategy\PneuTransformationStrategy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    public function __construct(
        private ApiPlatformConsumerService $apiService,
        private PneuTransformationStrategy $pneuTransformationStrategy,
    ) {}
    #[Route('/cart', name: 'cart')]
    public function index(Request $request): Response
    {
        $tiresDTO = $this->apiService->fetchPneus(1, 4);
        if (!$tiresDTO) {
            throw $this->createNotFoundException('The requested tire does not exist.');
        }
        $tires=[];
        foreach ($tiresDTO as $pneu) {
            $tires[] = $this->pneuTransformationStrategy->transform($pneu);
        }

        return $this->render('cart.twig', [
            'tires' => $tires,
        ]);
    }
}
