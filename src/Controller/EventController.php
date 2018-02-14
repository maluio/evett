<?php

namespace App\Controller;

use App\Provider\MeetupProvider;
use App\Provider\OvsProvider;
use App\Provider\ParisApiProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EventController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, ParisApiProvider $paris, OvsProvider $ovs, MeetupProvider $meetup)
    {
        $events = [];

        $date = new \DateTime();

        if($request->getQueryString()){
            $date->setDate(
                $request->get('year'),
                $request->get('month'),
                $request->get('day')
            );
        }

        //$events = array_merge($paris->get(), $events);
        $events = array_merge($ovs->get(clone $date), $events);
        $events = array_merge($meetup->get(clone $date), $events);
        usort($events, function($a, $b){
            if($a->getStart() > $b->getStart()){
                return 1;
            }
            if($a->getStart() < $b->getStart()){
                return -1;
            }
            return 0;
        });

        return $this->render('index.html.twig',
            [
                'events' => $events
            ]);
    }
}
