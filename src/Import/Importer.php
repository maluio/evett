<?php


namespace App\Import;


use App\Provider\ProviderManager;
use App\Repository\EventRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use GuzzleHttp\Client;

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

            $this->entityManager->persist($event);

            // flushing inside the for loop to prevent non-unique events
            //@todo: if performance becomes an issue, this can be improved
            $this->entityManager->flush();
        }
        $day = Carbon::instance($day);
        $message = count($events) . ' imported for ' . $day->toDateString();
        $client = new  Client();
        $client->request('POST', getenv('WEBHOOK_SEND_MESSAGE'), ['json' => ['text' => $message]]);
    }
}