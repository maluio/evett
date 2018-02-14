<?php

namespace App\Controller;

use App\Provider\MeetupProvider;
use App\Provider\OvsProvider;
use App\Provider\ParisApiProvider;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EventController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function index(ParisApiProvider $paris, OvsProvider $ovs, MeetupProvider $meetup)
    {
        $events = [];
        //$events = array_merge($paris->get(), $events);
        $events = array_merge($ovs->get(), $events);
        $events = array_merge($meetup->get(), $events);
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
