<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Repository\AdherentRepository;
use App\Repository\AvisRepository;
use App\Repository\PneuRepository;
use App\Services\ReviewManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ReviewsController extends AbstractController
{

    public function __construct(
        private ReviewManager $reviewManager,
    )
    {}

    #[Route('/reviews', name: 'app_reviews')]
    public function index(PneuRepository $pneuRepository): Response
    {
        $tiresWithReviews = $pneuRepository->findAllWithReviews();
        $uploadsBaseUrl = $this->getParameter('uploads_base_url');

        return $this->render('reviews/tires.twig', [
            'tiresWithReviews' => $tiresWithReviews,
            'uploads_base_url' => $uploadsBaseUrl,
        ]);
    }

    #[Route('/reviews/{slug}', name: 'app_tire_reviews')]
    public function reviews(string $slug, PneuRepository $pneuRepository, AvisRepository $avisRepository, AdherentRepository $adherentRepository): Response
    {
        $tire = $pneuRepository->findOneBy(['slug' => $slug]);

        if (!$tire) {
            throw $this->createNotFoundException('Tire not found');
        }

        $reviews = $avisRepository->findBy(['pneu' => $tire]);
        foreach ($reviews as &$review) {
            if ($review->getAdherent() !== null) {
                $adherent = $adherentRepository->find($review->getAdherent()->getId());
                $review->adherentName = $adherent->getNom();
                $review->adherentEmail = $adherent->getEmail();
            } else {
                $review->adherentName = $review->getAuthor();
                $review->adherentEmail = $review->getEmail();
            }
        }
        return $this->render('reviews/reviews.twig', [
            'tire' => $tire,
            'reviews' => $reviews,
        ]);
    }

    #[Route('/reviews/delete/{id}', name: 'app_delete_review', methods: ['DELETE'])]
    public function deleteReview(Avis $review): JsonResponse
    {
        if (!$review) {
            return new JsonResponse(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }
        $this->reviewManager->deleteReview($review);
        return new JsonResponse(['success' => 'Review deleted'], Response::HTTP_OK);
    }
}
