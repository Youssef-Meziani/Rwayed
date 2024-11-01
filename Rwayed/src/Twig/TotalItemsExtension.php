<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Entity\PneuFavList;
use App\Entity\Adherent;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TotalItemsExtension extends AbstractExtension
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private Security $security)
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('totalItems', [$this, 'getTotalItems']),
        ];
    }

    public function getTotalItems()
    {
        $user = $this->security->getUser();

        if ($user instanceof Adherent) {
            return $this->entityManager->getRepository(PneuFavList::class)->count(['adherent' => $user]);
        }

        return 0; // Retourne 0 si l'utilisateur n'est pas connecté ou n'est pas un Adherent
    }
}