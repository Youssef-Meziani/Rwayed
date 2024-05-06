<?php

namespace App\Services;

use App\Entity\Adresse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Adherent;

class AddressesService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
       
    }

    public function addAddress(Adresse $adresse,?Adherent $adherent): void
    {
           
        if (!$adherent) {
            throw new \LogicException('L\'utilisateur n\'est pas connecté.');
        }
        
     
        // Associez ici l'adhérent connecté à l'adresse
        $adresse->setAdherent($adherent);

        // Persistez la nouvelle adresse dans la base de données
        $this->entityManager->persist($adresse);
        $this->entityManager->flush();
    }

    public function updateAddress(Adresse $adresse): Adresse
    {
        // Enregistrez les modifications dans la base de données
        $this->entityManager->flush();

        return $adresse;
    }

    public function deleteAddress(Adresse $adresse): void
    {
        // Supprimez l'adresse de la base de données
        $this->entityManager->remove($adresse);
        $this->entityManager->flush();
    }
    public function setAsDefaultAddress(Adresse $adresse, Adherent $adherent): void
    {
        // Récupérer toutes les adresses de l'adhérent
        $addresses = $adherent->getAdresses();
    
        // Si l'adresse n'est pas déjà associée à l'adhérent, l'associer
        if (!$addresses->contains($adresse)) {
            $adresse->setAdherent($adherent);
            $addresses->add($adresse);
        }
    
        // Désactiver toutes les adresses par défaut précédemment définies pour l'adhérent
        foreach ($addresses as $memberAddress) {
            $memberAddress->setSetasmydefaultaddress(false);
        }
    
        // Activer l'adresse fournie comme l'adresse par défaut
        $adresse->setSetasmydefaultaddress(true);
    
        // Persist the changes
        $this->entityManager->persist($adresse);
        $this->entityManager->flush();
    }
    
    
}
