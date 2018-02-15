<?php


namespace App\Import;


use App\Provider\MeetupProvider;
use App\Provider\OvsProvider;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class Importer
{
    /**
     * @var MeetupProvider
     */
    private $meetupProvider;

    /**
     * @var OvsProvider
     */
    private $OvsProvider;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * Importer constructor.
     * @param MeetupProvider $meetupProvider
     * @param OvsProvider $OvsProvider
     * @param EventRepository $eventRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        MeetupProvider $meetupProvider,
        OvsProvider $OvsProvider,
        EventRepository $eventRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->meetupProvider = $meetupProvider;
        $this->OvsProvider = $OvsProvider;
        $this->eventRepository = $eventRepository;
        $this->entityManager = $entityManager;
    }

    public function import(\DateTime $day){
        $events = [];

        $events = array_merge($this->OvsProvider->get($day), $events);
        $events = array_merge($this->meetupProvider->get($day), $events);

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