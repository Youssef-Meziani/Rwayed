<?php
namespace App\Controller;

use App\Entity\Adherent;
use App\Services\ApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AdherentApiController extends AbstractController
{
    private $apiClient;

    public function __construct(ApiClient $apiClient) {
        $this->apiClient = $apiClient;
    }

    #[Route('/adherents', name: 'get_adherents')]
    public function getPersonnes() {
        $adherents = $this->apiClient->get('adherents', Adherent::class.'[]');
        dd($adherents);
    }

    #[Route('/adherents/{id}', name: 'get_adherent')]
    public function getAdherent(int $id): Response {
        $adherent = $this->apiClient->get('adherents/'.$id, Adherent::class);
        dd($adherent);
    }

    #[Route('/adherents/create', name: 'create_adherents')]
    public function createPersonne() {
        $adherent = new Adherent();
        $adherent->setNom('Doe');
        $adherent->setPrenom('John');
        $adherent->setTele('0123456789');
        $adherent->setDateNaissance(new \DateTime('1967-11-21'));
        $adherent->setDernierConnection(new \DateTime());
        $adherent->setEmail("test@example.com");
        $adherent->setPassword("123azert");

        $createdAdherent = $this->apiClient->post('adherents', $adherent, []);
        dd($createdAdherent);
    }

    #[Route('/adherents/update/{id}', name: 'update_aadherents')]
    public function updateAdherent(int $id) {
        // Simule la récupération d'une instance d'Adherent à modifier
        $adherent = new Adherent();
        $adherent->setNom('Doe Updated');
        $adherent->setPrenom('John Updated');
        $adherent->setTele('0123456789');
        $adherent->setDateNaissance(new \DateTime('1967-11-21'));
        $adherent->setDernierConnection(new \DateTime());
        $adherent->setEmail("test@example.com");
        $adherent->setPassword("123azert");
        // Plus de setters selon votre entité Adherent

        // Suppose que l'API nécessite l'ID dans l'URI pour la modification
        $updatedAdherent = $this->apiClient->put("adherents/{$id}", $adherent, Adherent::class);

        dd($updatedAdherent);
    }

    #[Route('/adherents/delete/{id}', name: 'delete_adherents')]
    public function deleteAdherent(int $id) {
        // Appelle l'API pour supprimer l'Adherent
        $response = $this->apiClient->delete("adherents/{$id}");

        if ($response['error'] ?? false) {
            // Gérer l'erreur, par exemple, enregistrer un message flash et rediriger
            $this->addFlash('error', 'Failed to delete the adherent.');
            return $this->redirectToRoute('list_adherents');
        }

        // Confirmation de la suppression et redirection
        $this->addFlash('success', 'Adherent successfully deleted.');
        return $this->redirectToRoute('list_adherents');
    }
}
