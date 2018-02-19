<?php


namespace App\Import;


use App\Provider\ProviderManager;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Importer constructor.
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     * @param ProviderManager $providers
     */
    public function __construct(
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager,
        ProviderManager $providers,
        LoggerInterface $logger
    ) {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
        $this->providerManager = $providers;
        $this->logger = $logger;
    }

    public function import(\DateTime $day){
        $events = [];

        foreach ($this->providerManager->getAll() as $provider){
            $this->logger->info('Import started for ' . $provider->getName());
            try {
                $events = array_merge($provider->getEvents($day), $events);
            }
            catch (\Exception $e) {
                $this->logger->error('Error during event import: ' . $e->getMessage());
            }
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