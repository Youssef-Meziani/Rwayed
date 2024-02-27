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
