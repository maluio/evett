<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function urlExists ($url){
        $existingRecords = $this->findBy(['url' => $url]);
        dump( count($existingRecords));
        return count($existingRecords) ? true : false;
    }

    public function getForDay(\DateTime $day)
    {
        $from = new \DateTime($day->format("Y-m-d")." 00:00:00");
        $to   = new \DateTime($day->format("Y-m-d")." 23:59:59");

        $qb = $this->createQueryBuilder("e");
        $qb
            ->andWhere('e.hidden = 0')
            ->andWhere('e.start BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->orderBy('e.start')
        ;
        return $qb->getQuery()->getResult();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('e')
            ->where('e.something = :value')->setParameter('value', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
