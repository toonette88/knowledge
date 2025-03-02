<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Order entity.
 *
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Finds all order details for a specific user.
     *
     * @param User $user The user whose order details are to be fetched.
     * @return array The result of the query.
     */
    public function findOrderDetailsByUser(User $user)
    {
        // Create a query builder for fetching orders by a specific user.
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.orderDetails', 'od') // Join orderDetails of the order.
            ->leftJoin('od.course', 'c') // Join related courses in the orderDetails.
            ->leftJoin('od.lesson', 'l') // Join related lessons in the orderDetails.
            ->addSelect('od', 'c', 'l') // Select the joined tables.
            ->where('o.user = :user') // Filter by the user.
            ->setParameter('user', $user) // Bind the user parameter to the query.
            ->indexBy('o', 'o.id'); // Group results by the order ID.

        // Execute the query and return the result.
        return $qb->getQuery()->getResult();
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
     * public function findOneBySomeField($value): ?Order
     * {
     *     return $this->createQueryBuilder('o')
     *         ->andWhere('o.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
