<?php

namespace App\Repository;

use App\Entity\OrderDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the OrderDetail entity.
 *
 * @extends ServiceEntityRepository<OrderDetail>
 */
class OrderDetailRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDetail::class);
    }

    /*
     * Example methods that can be implemented as needed:
     *
     * public function findByExampleField($value): array
     * {
     *     return $this->createQueryBuilder('o')
     *         ->andWhere('o.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->orderBy('o.id', 'ASC')
     *         ->setMaxResults(10)
     *         ->getQuery()
     *         ->getResult();
     * }
     *
     * public function findOneBySomeField($value): ?OrderDetail
     * {
     *     return $this->createQueryBuilder('o')
     *         ->andWhere('o.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
