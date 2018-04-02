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

    public function findOneByUrl($url){
        return $existingRecords = $this->findOneBy(['url' => $url]);
    }

    public function findForDay(\DateTime $day, string $provider=null, $hourOffset=null)
    {
        $qb = $this->getForDayQueryBuilder($day, $hourOffset);

        if($provider){
            $qb = $this->addQbForProvider($provider, $qb);
        }

        return $qb->getQuery()->getResult();
    }

    protected function addQbForProvider(string $providerName, $qb){
       // $qb = $this->getForDayQueryBuilder($day);
        $qb->andWhere('e.provider = :pname')
            ->setParameter('pname', $providerName);
        return $qb;
    }

    protected function getForDayQueryBuilder(\DateTime $day, $hourOffset=null){
        $from = new \DateTime($day->format("Y-m-d")." 00:00:00");
        if($hourOffset) {
            $from->modify('+ '. (int) $hourOffset . ' hours');
        }
        $to = new \DateTime($day->format("Y-m-d")." 23:59:59");

        $qb = $this->createQueryBuilder("e");
        $qb
            ->andWhere('e.hidden = 0')
            ->andWhere('e.start BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
            ->orderBy('e.start')
        ;

        return $qb;
    }

    public function findUpcomingStarred(){
        $now = new \DateTime();
        $today = new \DateTime($now->format("Y-m-d")." 00:00:00");

        $qb = $this->createQueryBuilder('e');
        $qb->where('e.starred = true')
            ->andWhere('e.start > :today')
            ->setParameter('today', $today)
            ->orderBy('e.start')
        ;

        return $qb->getQuery()->getResult();
    }
}
