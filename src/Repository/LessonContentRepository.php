<?php

namespace App\Repository;

use App\Entity\LessonContent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for the LessonContent entity.
 *
 * @extends ServiceEntityRepository<LessonContent>
 */
class LessonContentRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry The Doctrine registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonContent::class);
    }

    /*
     * Example methods that can be implemented if needed:
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
     * public function findOneBySomeField($value): ?LessonContent
     * {
     *     return $this->createQueryBuilder('l')
     *         ->andWhere('l.exampleField = :val')
     *         ->setParameter('val', $value)
     *         ->getQuery()
     *         ->getOneOrNullResult();
     * }
     */
}
