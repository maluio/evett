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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Report
     */
    private $report;

    public function __construct(
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager,
        ProviderManager $providers,
        LoggerInterface $logger,
        ValidatorInterface $validator,
        Report $report
    ) {
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
        $this->providerManager = $providers;
        $this->logger = $logger;
        $this->validator = $validator;
        $this->report = $report;
    }

    public function import(Carbon $day): Importer {

        foreach ($this->providerManager->getAll() as $provider){
            $providerReport = new ProviderReport($provider->getKey());
            $this->report->day = $day->copy();
            $this->logger->info('Import started for ' . $provider->getKey());

            try {
                $events = $provider->getEvents($day->copy());
                $providerReport->numberOfFoundEvents = count($events);
            }
            catch (\Exception $e) {
                $this->logger->error('Error from provider during event import: ' . $e->getMessage());
            }

            foreach ($events as $event){
                if($this->persistEvent($event)){
                    $providerReport->numberOfImportedEvents++;
                }
            }
            $this->report->addProviderReport($providerReport);
        }

        return $this;
    }

    private function persistEvent(Event $event): bool {
        $uniqueIdentifier = md5($event->getUrl() . $event->getStart()->toDateString());

        if(!$this->eventRepository->isEventNew($uniqueIdentifier)){
            return false;
        }

        $event->setUniqueIdentifier($uniqueIdentifier);

        $errors = $this->validator->validate($event);
        if(count($errors) > 0){
            foreach ($errors as $error){
                $this->logger->error('Import validation failed for: ' . $event->getUrl(),  [$error->__toString()]);
            }
        }

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return true;
    }

    public function getReport(): Report{
        return $this->report;
    }

    public function getMessage(): string {
        return $this->report->getMessage();
    }
}