<?php

namespace App\Repository;

use App\Entity\Pneu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
     * Finds all tires with at least one review and their review count.
     *
     * @return array
     */
    public function findAllWithReviews(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p as tire', 'COUNT(a.id) as reviewCount')
            ->leftJoin('p.avis', 'a')
            ->groupBy('p.id')
            ->having('COUNT(a.id) > 0')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Pneu[] Returns an array of Pneu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pneu
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}