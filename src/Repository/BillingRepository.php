<?php

namespace App\Repository;

use App\Entity\Billing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Billing entity.
 *
 * @extends ServiceEntityRepository<Billing>
 */
class BillingRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Billing::class);
    }

    /*
     * Example methods that can be implemented if needed:
     *
     * public function findByExampleField($value): array
     * {
     *     return $this->createQueryBuilder('b')
     *         ->andWhere('b.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->orderBy('b.id', 'ASC')
     *         ->setMaxResults(10)
     *         ->getQuery()
     *         ->getResult();
     * }
     *
     * public function findOneBySomeField($value): ?Billing
     * {
     *     return $this->createQueryBuilder('b')
     *         ->andWhere('b.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
