<?php

namespace App\Controller;

use App\Entity\Event;
use App\Provider\MeetupProvider;
use App\Provider\OvsProvider;
use App\Provider\ParisApiProvider;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            $date->setDate(
                $request->get('year'),
                $request->get('month'),
                $request->get('day')
            );
        }

        return clone $date;
    }
}
