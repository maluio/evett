<?php


namespace App\EventListener;


use App\Entity\Event;
use Doctrine\ORM\Event\LifecycleEventArgs;

class EventIsViewedUpdater
{
    public function postLoad(LifecycleEventArgs $args){

        $entity = $args->getEntity();

        if (!$entity instanceof Event) {
            return;
        }

        if($entity->isViewed()){
            return;
        }

        $entityManager = $args->getEntityManager();

        $entity->setIsViewed(true);

        $entityManager->persist($entity);

        $entityManager->flush();
    }
}