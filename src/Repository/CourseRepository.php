<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Course entity.
 *
 * @extends ServiceEntityRepository<Course>
 */
class CourseRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * Retrieves all courses with their associated lessons.
     *
     * @return Course[] Returns an array of Course objects with related lessons.
     */
    public function findAllWithLessons(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.lessons', 'lesson') // Left join to fetch lessons related to courses
            ->addSelect('lesson') // Ensures lessons are included in the result
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
     * public function findOneBySomeField($value): ?Course
     * {
     *     return $this->createQueryBuilder('c')
     *         ->andWhere('c.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
