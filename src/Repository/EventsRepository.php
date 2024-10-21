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

    /**
     * @param $value
     * @return array|null
     */
    public function findEventByDate($value): ?array
    {
        $events = $this->createQueryBuilder('e')
            ->where('e.startAt > :now')
            ->setParameter('now', $value)
            ->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        return !empty($events) ? $events : null;
    }

    /**
     * @param $value
     * @return array|null
     */
    public function findByUserSuscribed($value): ?array
    {
        $events = $this->createQueryBuilder('e')
            ->where('e.startAt > :now')
            ->andWhere(':user MEMBER OF e.suscribers')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('user', $value)
            ->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        return !empty($events) ? $events : null;
    }

    /**
     * @param $value
     * @return array|null
     */
    public function findByCompletedEvents($value): ?array
    {
        $events = $this->createQueryBuilder('e')
            ->where('e.startAt < :now')
            ->andWhere('e.user = :user OR :user MEMBER OF e.suscribers')
            ->setParameter('now', new \DateTimeImmutable())
            ->setParameter('user', $value)
            ->orderBy('e.startAt', 'ASC')
            ->getQuery()
            ->getResult();

        return !empty($events) ? $events : null;
    }
}
