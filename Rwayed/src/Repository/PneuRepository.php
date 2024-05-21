<?php

namespace App\Repository;

use App\Entity\Pneu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pneu>
 *
 * @method Pneu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pneu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pneu[]    findAll()
 * @method Pneu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PneuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pneu::class);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countOrdersForPneu($pneuId): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(lc.id)')
            ->innerJoin('p.ligneCommandes', 'lc')
            ->where('p.id = :id')
            ->setParameter('id', $pneuId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
