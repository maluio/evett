<?php


namespace App\Import;


use App\Entity\Event;
use App\Provider\ProviderManager;
use App\Repository\EventRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var int
     */
    private $numberOfImportedEvents=0;

    /**
     * @var int
     */
    private $numberOfFoundEvents=0;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager,
        ProviderManager $providers,
        LoggerInterface $logger,
        ValidatorInterface $validator
    ) {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
        $this->providerManager = $providers;
        $this->logger = $logger;
        $this->validator = $validator;
    }

    public function import(Carbon $day): string {
        $events = [];

        foreach ($this->providerManager->getAll() as $provider){
            $this->logger->info('Import started for ' . $provider->getKey());
            try {
                $events = array_merge($provider->getEvents($day), $events);
            }
            catch (\Exception $e) {
                $this->logger->error('Error from provider during event import: ' . $e->getMessage());
            }
        }
        $this->numberOfFoundEvents = count($events);
        $this->numberOfImportedEvents = 0;
        foreach ($events as $event){
            $this->persistEvent($event);
        }

        $message = $this->getNumberOfFoundEvents() . ' events found, ' . $this->getNumberOfImportedEvents(). ' imported';
        $message .= ' for ' . $day->toDateString();

        return $message;
    }

    private function persistEvent(Event $event): void {
        $uniqueIdentifier = md5($event->getUrl() . $event->getStart()->toDateString());

        if(!$this->eventRepository->isEventNew($uniqueIdentifier)){
            return;
        }

        $event->setUniqueIdentifier($uniqueIdentifier);

        $errors = $this->validator->validate($event);
        if(count($errors) > 0){
            foreach ($errors as $error){
                $this->logger->error('Import validation failed for: ' . $event->getUrl(),  [$error->__toString()]);
            }
        }

        $this->numberOfImportedEvents++;
        $this->entityManager->persist($event);
        $this->entityManager->flush();
    }

    public function getNumberOfImportedEvents(): int
    {
        return $this->numberOfImportedEvents;
    }

    public function getNumberOfFoundEvents(): int
    {
        return $this->numberOfFoundEvents;
    }
}