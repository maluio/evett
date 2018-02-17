<?php


namespace App\Import;


use App\Provider\ProviderManager;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class Importer
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ProviderManager
     */
    private $providerManager;

    /**
     * Importer constructor.
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     * @param ProviderManager $providers
     */
    public function __construct(
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager,
        ProviderManager $providers
    ) {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
        $this->providerManager = $providers;
    }

    public function import(\DateTime $day){
        $events = [];

        foreach ($this->providerManager->getAll() as $provider){
            $events = array_merge($provider->getEvents($day), $events);
        }

        $updated = new \DateTime();

        foreach ($events as $event){
            if($existingEvent = $this->eventRepository->findOneByUrl($event->getUrl())){
               $event = $existingEvent;
            }
            $event->setUpdated($updated);
            $this->entityManager->persist($event) ;
        }

        $this->entityManager->flush();
    }
}