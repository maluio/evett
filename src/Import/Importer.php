<?php


namespace App\Import;


use App\Provider\ProviderManager;
use App\Repository\EventRepository;
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
     * @var ValidatorInterface
     */
    private $validator;

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
        LoggerInterface $logger,
        ValidatorInterface $validator
    ) {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
        $this->providerManager = $providers;
        $this->logger = $logger;
        $this->validator = $validator;
    }

    public function import(\DateTime $day){
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

        $updated = new \DateTime();
        $this->numberOfImportedEvents = 0;
        foreach ($events as $event){
            if($existingEvent = $this->eventRepository->findOneByUrl($event->getUrl())){
               $event = $existingEvent;
            }
            $event->setUpdated($updated);

            $errors = $this->validator->validate($event);

            if(count($errors) > 0){
                foreach ($errors as $error){
                    $this->logger->error('Validation failed for: ' . $event->getUrl(),  [$error->__toString()]);
                }
            }
            $this->numberOfImportedEvents++;
            $this->entityManager->persist($event);
            $this->entityManager->flush();
        }
    }

    /**
     * @return int
     */
    public function getNumberOfImportedEvents(): int
    {
        return $this->numberOfImportedEvents;
    }
}