<?php

namespace App\Controller;

use App\Entity\Event;
use App\Import\Importer;
use App\Provider\ProviderManager;
use App\Repository\EventRepository;
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
        Importer $importer,
        ProviderManager $providerManager
    )
    {
        $day = $this->getDay($request);
        $events = $eventRepository->getForDay(
            $day,
            $request->get('provider')
        );
        $oneHourAgo = new \DateTime('now - 1hour');

        if(
            !count($events)
            || $events[0]->getUpdated() < $oneHourAgo
        )
        {
            $importer->import($day);
            $events = $eventRepository->getForDay($day);
        }

        return $this->render('index.html.twig',
            [
                'events' => $events,
                'day' => $day,
                'provider' => $providerManager->getAll()
            ]);
    }

    /**
     * @Route("/hide/{id}", name="hide")
     */
    public function hide(Event $event, Request $request){
        $event->setHidden(true);
        $this->getDoctrine()->getManager()->flush();

        $referer = $request->headers->get('referer');

        return $this->redirect($referer);
    }

    /**
     * @param Request $request
     * @return \DateTime
     */
    private function getDay(Request $request){
        $date = new \DateTime();

        if($request->getQueryString()){
            if(
                $request->get('year')
                && $request->get('month')
                && $request->get('day')
            )
            $date->setDate(
                $request->get('year'),
                $request->get('month'),
                $request->get('day')
            );
        }

        return clone $date;
    }
}
