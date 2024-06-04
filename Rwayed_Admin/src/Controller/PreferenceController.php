<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PreferenceController extends AbstractController
{
    #[Route('/save-preferences', name: 'save_preferences', methods: ['POST'])]
    public function savePreferences(Request $request, SessionInterface $session): JsonResponse
    {
        $preferences = json_decode($request->getContent(), true);

        foreach ($preferences as $key => $value) {
            $session->set($key, $value);
        }

        return new JsonResponse(['message' => 'Preferences saved successfully']);
    }

    #[Route('/get-preferences', name: 'get_preferences', methods: ['GET'])]
    public function getPreferences(SessionInterface $session): JsonResponse
    {
        $preferences = [
            'layout' => $session->get('layout', 'vertical'),
            'layout-mode' => $session->get('layout-mode', 'light'),
            'layout-width' => $session->get('layout-width', 'fluid'),
            'layout-position' => $session->get('layout-position', 'fixed'),
            'topbar-color' => $session->get('topbar-color', 'light'),
            'sidebar-size' => $session->get('sidebar-size', 'lg'),
            'sidebar-color' => $session->get('sidebar-color', 'light'),
            'layout-direction' => $session->get('layout-direction', 'ltr'),
        ];

        return new JsonResponse($preferences);
    }
}
