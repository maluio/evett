<?php


namespace App\EventListener;


use App\Entity\Event;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EventLastViewedUpdater
{
    public function postLoad(LifecycleEventArgs $args){

        $entity = $args->getEntity();

        if (!$entity instanceof Event) {
            return;
        }

        $entityManager = $args->getEntityManager();

        $entity->setLastViewed(new \DateTime());

        $entityManager->persist($entity);

        $entityManager->flush();
    }
}