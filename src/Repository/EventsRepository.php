<?php

namespace App\Repository;

use App\Entity\Events;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Events>
 */
class EventsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Events::class);
    }

    //    /**
    //     * @return Events[] Returns an array of Events objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Events
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findByTags($start, $end)
    {
        $queryBuilder = $this->createQueryBuilder('e');

        if ($start) {
            $queryBuilder->andWhere('e.startAt >= :startAt')
                ->setParameter('startAt', $start);
        }

        if ($end) {
            $queryBuilder->andWhere('e.endAt <= :endAt')
                ->setParameter('endAt', $end);
        }

        if ($start || $end) {
            $queryBuilder->orWhere('e.startAt BETWEEN :startAt AND :endAt')
                ->orWhere('e.endAt BETWEEN :startAt AND :endAt')
                ->orWhere('e.startAt <= :endAt AND e.endAt >= :startAt')
                ->setParameter('startAt', $start)
                ->setParameter('endAt', $end)
                ->orderBy('e.startAt', 'ASC');
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
