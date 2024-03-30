<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Services\ApiPlatformConsumerService;
use App\Strategy\PneuTransformationStrategy;

class ProductController extends AbstractController
{

    private ApiPlatformConsumerService $apiService;
    private $pneuTransformationStrategy;

    public function __construct(ApiPlatformConsumerService $apiService, PneuTransformationStrategy $pneuTransformationStrategy)
    {
        $this->apiService = $apiService;
        $this->pneuTransformationStrategy = $pneuTransformationStrategy;
    }

    #[Route('/quickview/{id}', name: 'quickview')]
    public function quickview(int $id, Request $request): Response
    {
         if (!$request->isXmlHttpRequest()) {
             throw new NotFoundHttpException();
         }

        try {
            // fetchPneuById pour récupérer les données du pneu
            $pneuDTO = $this->apiService->fetchPneuById($id);
            $pneu = $this->pneuTransformationStrategy->transform($pneuDTO);
        } catch (\Exception $e) {
            throw new NotFoundHttpException('Pneu not found');
        }
        // minio
        return $this->render('partials/_quickview.twig', [
            'pneu' => $pneu,
            'id' => $id,
        ]);
    }
}
