<?php

namespace App\Controller;

use App\Provider\MeetupProvider;
use App\Provider\OvsProvider;
use App\Provider\ParisApiProvider;
use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, EventRepository $eventRepository)
    {
        $day = $this->getDay($request);
        $events = $eventRepository->getForDay($day);

        return $this->render('index.html.twig',
            [
                'events' => $events
            ]);
    }

    /**
     * @Route("/import/", name="import")
     */
    public function import(Request $request, ParisApiProvider $paris, OvsProvider $ovs, MeetupProvider $meetup){
        $ovs->get($this->getDay($request));
        $meetup->get($this->getDay($request));

        return $this->redirectToRoute('home');
    }


    /**
     * @param Request $request
     * @return \DateTime
     */
    private function getDay(Request $request){
        $date = new \DateTime();

        if($request->getQueryString()){
            $date->setDate(
                $request->get('year'),
                $request->get('month'),
                $request->get('day')
            );
        }

        return clone $date;
    }
}
