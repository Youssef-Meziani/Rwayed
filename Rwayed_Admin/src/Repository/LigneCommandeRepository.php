<?php

namespace App\Repository;

use App\Entity\LigneCommande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LigneCommande>
 *
 * @method LigneCommande|null find($id, $lockMode = null, $lockVersion = null)
 * @method LigneCommande|null findOneBy(array $criteria, array $orderBy = null)
 * @method LigneCommande[]    findAll()
 * @method LigneCommande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LigneCommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LigneCommande::class);
    }

    public function findAllWithAdherent()
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT lc, c, a
                FROM App\Entity\LigneCommande lc
                JOIN lc.commande c
                JOIN c.adherent a
            ')
            ->getResult(AbstractQuery::HYDRATE_ARRAY);  // Use HYDRATE_ARRAY to get a more straightforward array result, or remove it for objects
    }
}
