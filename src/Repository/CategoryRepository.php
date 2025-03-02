<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Category entity.
 *
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Retrieves all categories with their associated courses.
     *
     * @return Category[] Returns an array of Category objects with related courses.
     */
    public function findAllWithCourses(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.courses', 'course') // Left join to include courses in the result
            ->addSelect('course') // Ensures courses are fetched along with categories
            ->getQuery()
            ->getResult();
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
     * public function findOneBySomeField($value): ?Category
     * {
     *     return $this->createQueryBuilder('c')
     *         ->andWhere('c.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
