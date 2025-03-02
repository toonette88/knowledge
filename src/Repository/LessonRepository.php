<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the Lesson entity.
 *
 * @extends ServiceEntityRepository<Lesson>
 */
class LessonRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    /*
     * Example methods that can be implemented as needed:
     *
     * public function findByExampleField($value): array
     * {
     *     return $this->createQueryBuilder('l')
     *         ->andWhere('l.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->orderBy('l.id', 'ASC')
     *         ->setMaxResults(10)
     *         ->getQuery()
     *         ->getResult();
     * }
     *
     * public function findOneBySomeField($value): ?Lesson
     * {
     *     return $this->createQueryBuilder('l')
     *         ->andWhere('l.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
