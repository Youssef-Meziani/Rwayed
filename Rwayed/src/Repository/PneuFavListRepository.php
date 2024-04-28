<?php

namespace App\Repository;

use App\Entity\PneuFavList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PneuFavList>
 *
 * @method PneuFavList|null find($id, $lockMode = null, $lockVersion = null)
 * @method PneuFavList|null findOneBy(array $criteria, array $orderBy = null)
 * @method PneuFavList[]    findAll()
 * @method PneuFavList[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PneuFavListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PneuFavList::class);
    }

//    /**
//     * @return PneuFavList[] Returns an array of PneuFavList objects
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

//    public function findOneBySomeField($value): ?PneuFavList
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
