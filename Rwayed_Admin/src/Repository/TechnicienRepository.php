<?php

namespace App\Repository;

use App\Entity\Technicien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Technicien>
 *
 * @method Technicien|null find($id, $lockMode = null, $lockVersion = null)
 * @method Technicien|null findOneBy(array $criteria, array $orderBy = null)
 * @method Technicien[]    findAll()
 * @method Technicien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TechnicienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Technicien::class);
    }

    /**
     * @throws Exception
     */
    public function getTotalTechniciensCount(): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT COUNT(*) as total_techniciens FROM technicien';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

        return (int) $resultSet->fetchOne();
    }
}
