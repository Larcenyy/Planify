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

    /**
     * @param $start
     * @param $end
     * @return mixed
     */
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
