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

    public function findMostSoldPneus(): array|float|int|string
    {
        return $this->createQueryBuilder('p')
            ->join('p.ligneCommandes', 'lc')  // Change from leftJoin to inner join
            ->select('p as pneu', 'SUM(lc.quantity) as totalSold')
            ->groupBy('p.id')
            ->having('SUM(lc.quantity) > 0')  // Ensure that only pneus with orders are selected
            ->orderBy('totalSold', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getArrayResult(); // This returns results as arrays
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countPneusBySeason(): array
    {
        // First, get the total count of pneus to calculate proportions
        $totalQuery = $this->createQueryBuilder('p')
            ->select('COUNT(p.id) as totalPneus')
            ->getQuery()
            ->getSingleScalarResult();

        if ($totalQuery > 0) {
            // Now fetch the count of pneus per season
            $results = $this->createQueryBuilder('p')
                ->select("p.saison as season, COUNT(p.id) as count")
                ->groupBy('p.saison')
                ->getQuery()
                ->getResult();

            // Calculate the proportion of each season's pneus as a percentage of the total
            return array_map(function ($result) use ($totalQuery) {
                $result['proportion'] = ($result['count'] / $totalQuery) * 100; // Calculate percentage
                return $result;
            }, $results);
        }

        return []; // Return an empty array if no pneus exist to avoid division by zero
    }


}