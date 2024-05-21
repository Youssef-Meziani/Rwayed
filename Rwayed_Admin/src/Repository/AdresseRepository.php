<?php

namespace App\Repository;

use App\Entity\Adresse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Adresse>
 *
 * @method Adresse|null find($id, $lockMode = null, $lockVersion = null)
 * @method Adresse|null findOneBy(array $criteria, array $orderBy = null)
 * @method Adresse[]    findAll()
 * @method Adresse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdresseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Adresse::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findDefaultAddressByAdherent($adherentId): ?Adresse
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.adherent = :adherentId')
            ->andWhere('a.setasmydefaultaddress = :default')
            ->setParameter('adherentId', $adherentId)
            ->setParameter('default', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
