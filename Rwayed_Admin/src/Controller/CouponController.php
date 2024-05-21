<?php

namespace App\Controller;

use App\Entity\CodePromo;
use App\Form\CodePromoType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[IsGranted("ROLE_ADMIN")]
class CouponController extends AbstractController
{
    #[Route('/coupon', name: 'coupon_index')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $coupon = new CodePromo();
        $form = $this->createForm(CodePromoType::class, $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($coupon);
                $entityManager->flush();

                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => true]);
                }

                $this->addFlash('success', 'Coupon added successfully.');
                return $this->redirectToRoute('coupon_index');
            } catch (UniqueConstraintViolationException $e) {
                if ($request->isXmlHttpRequest()) {
                    return new JsonResponse(['success' => false, 'errors' => ['code' => ['This code is already in use. Please choose another one.']]]);
                }
                $this->addFlash('error', 'This code is already in use. Please choose another one.');
            }
        }

        if ($request->isXmlHttpRequest()) {
            $errors = [];
            foreach ($form->getErrors(true, true) as $error) {
                $errors[$error->getOrigin()->getName()][] = $error->getMessage();
            }
            return new JsonResponse(['success' => false, 'errors' => $errors]);
        }

        return $this->render('coupon/index.twig', [
            'couponForm' => $form->createView(),
        ]);
    }

    #[Route('/delete-coupon/{id}', name: 'delete_coupon', methods: ['POST'])]
    public function deleteCoupon(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $coupon = $entityManager->getRepository(CodePromo::class)->find($id);

        if (!$coupon) {
            return new JsonResponse(['error' => 'Coupon not found'], Response::HTTP_NOT_FOUND);
        }

        // Remove coupon from any associated orders
        foreach ($coupon->getCommande() as $commande) {
            $commande->setCodePromo(null);
        }

        try {
            $entityManager->remove($coupon);
            $entityManager->flush();
            return new JsonResponse(['success' => 'Coupon deleted successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to delete coupon'], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('coupon/edit/{id}', name: 'code_promo_edit')]
    public function edit(Request $request, CodePromo $codePromo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CodePromoType::class, $codePromo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->flush();
                if ($request->isXmlHttpRequest()) {
                    return $this->json(['success' => true]);
                }
                $this->addFlash('success', 'Coupon has been successfully modified.');
                return $this->redirectToRoute('coupon_index');
            } catch (UniqueConstraintViolationException $e) {
                if ($request->isXmlHttpRequest()) {
                    return $this->json(['success' => false, 'errors' => ['code' => ['This code is already in use. Please choose another one.']]]);
                }
                $this->addFlash('error', 'This code is already in use. Please choose another one.');
            }
        }

        if ($request->isXmlHttpRequest()) {
            // Handle AJAX request
            if (!$form->isValid()) {
                $errors = [];
                foreach ($form->getErrors(true) as $error) {
                    $errors[$error->getOrigin()->getName()][] = $error->getMessage();
                }
                return $this->json(['success' => false, 'errors' => $errors]);
            }
        }

        return $this->render('coupon/edit.twig', [
            'codePromo' => $codePromo,
            'couponForm' => $form->createView(),
        ]);
    }

    #[Route('/fetch-coupons', name: 'fetch_coupons')]
    public function fetchCoupons(EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $coupons = $entityManager->getRepository(CodePromo::class)->findAll();

        $jsonContent = $serializer->serialize($coupons, 'json', [
            AbstractNormalizer::ATTRIBUTES => ['code', 'description', 'pourcentage', 'dateExpiration', 'status', 'id'],
            'datetime_format' => 'Y-m-d H:i'
        ]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }
}
