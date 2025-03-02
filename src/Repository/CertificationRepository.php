<?php

namespace App\Repository;

use App\Entity\Certification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Certification entity.
 *
 * @extends ServiceEntityRepository<Certification>
 */
class CertificationRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certification::class);
    }

    /*
     * Example methods that can be implemented if needed:
     *
     * public function findByExampleField($value): array
     * {
     *     return $this->createQueryBuilder('c')
     *         ->andWhere('c.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->orderBy('c.id', 'ASC')
     *         ->setMaxResults(10)
     *         ->getQuery()
     *         ->getResult();
     * }
     *
     * public function findOneBySomeField($value): ?Certification
     * {
     *     return $this->createQueryBuilder('c')
     *         ->andWhere('c.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
