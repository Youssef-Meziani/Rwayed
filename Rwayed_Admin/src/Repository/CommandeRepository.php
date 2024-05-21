<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 *
 * @method Commande|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commande|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commande[]    findAll()
 * @method Commande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    /**
     * @throws Exception
     */
    public function getMonthlyOrderCount(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT MONTH(date_commande) as month, COUNT(*) as count
            FROM commande
            GROUP BY month
            ORDER BY month
        ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getTotalSalesAmount(): float
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT SUM(total) as total_sales FROM commande';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return (float) $resultSet->fetchOne();
    }

    /**
     * @throws Exception
     */
    public function getTotalOrderCount(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(*) as total_orders FROM commande';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return (int) $resultSet->fetchOne();
    }

}
