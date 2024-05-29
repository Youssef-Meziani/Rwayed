<?php

namespace App\Repository;

use App\Entity\Adherent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Adherent>
 *
 * @method Adherent|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adherent|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adherent[]    findAll()
 * @method Adherent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdherentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adherent::class);
    }

    /**
     * @throws Exception
     */
    public function getTotalAdherentsCount(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(*) as total_adherents FROM adherent';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return (int) $resultSet->fetchOne();
    }

    public function findTopAdherentsByPoints()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.points_fidelite', 'DESC')
            ->setMaxResults(10)  // Limit to top 10
            ->getQuery()
            ->getResult();
    }
}
