<?php

namespace App\Controller;

use App\Services\ApiPlatformConsumerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AboutUsController extends AbstractController
{
    
    private ApiPlatformConsumerService $apiService;

    public function __construct(ApiPlatformConsumerService $apiService)
    {
        $this->apiService = $apiService;
    }
    
    #[Route('/about', name: 'about')]
    public function yearexperience(): Response
    {
        // Calculate years of experience and augmentation
        $currentYear = date('Y');
        $projectStartDate = 2022; // Replace with your project start year
        $yearsSinceStart = $currentYear - $projectStartDate;
        $baseExperience = 1; // Start with 2 years of experience
        $augmentationPerYear = 1; // Increase by 1 every year
        $totalExperience = $baseExperience + max(0, $yearsSinceStart - 1) * $augmentationPerYear;
/*Elle commence par l'expérience de base, puis ajoute l'augmentation en fonction du nombre d'années écoulées depuis le début du projet.
 La fonction max(0, $yearsSinceStart - 1) est utilisée pour garantir que l'augmentation commence à partir de la deuxième année. 
 Si $yearsSinceStart est inférieur ou égal à 1 (ce qui signifie que c'est la première ou la deuxième année), il est ajusté 
 à 0 pour éviter une expérience négative.*/
     // Récupérer le nombre total de pneus depuis le service API
     $totalItems = $this->apiService->getTotalItems();
 $totalMembers = $this->apiService->getTotalMembers();
        // Render the template with the variables
        return $this->render('about-us.twig', [
            'totalExperience' => $totalExperience,
            'augmentationPerYear' => $augmentationPerYear,
            'totalItems' => $totalItems,
            'totalMembers' => $totalMembers,
        ]);
    }
    
    public function index(): Response
    {
        return $this->render('about-us.twig');
    }

}