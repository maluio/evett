<?php

namespace App\Controller;

use App\Import\Importer;
use App\Provider\ProviderManager;
use App\Repository\EventRepository;
use Carbon\Carbon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function index(
        Request $request,
        EventRepository $eventRepository,
        ProviderManager $providerManager
    )
    {
        $day = $this->getDay($request);
        $events = $eventRepository->findForDay(
            $day,
            $request->get('provider'),
            $request->get('hourOffset')
        );

        return $this->render('index.html.twig',
            [
                'events' => $events,
                'day' => $day,
                'provider' => $providerManager->getAll()
            ]);
    }

    /**
     * @Route("/starred/", name="starred_index")
     */
    public function starred(EventRepository $eventRepository){
        $starredEvents = $eventRepository->findUpcomingStarred();

        $eventsByDay = [];

        foreach ($starredEvents as $event){
            $eventsByDay[$event->getStart()->format('Y-m-d')][] = $event;
        }

        return $this->render('index.html.twig', ['eventsByDay'=>$eventsByDay]);
    }

    /**
     * @Route("/import/", name="import")
     */
    public function import(Request $request, Importer $importer){
        $day = $this->getDay($request);

        $message = $importer->import($day);
        $this->addFlash('notice', $message);

        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * @param Request $request
     * @return Carbon
     */
    private function getDay(Request $request){
        $date = Carbon::today();

        if($request->getQueryString()){
            if(
                $request->get('date')
            )
            $date = Carbon::createFromFormat('Y-m-d', $request->get('date'));
        }

        return clone $date;
    }
}
