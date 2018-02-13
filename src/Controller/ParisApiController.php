<?php

namespace App\Controller;

use App\Entity\Event;
use GuzzleHttp\Client;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParisApiController extends Controller
{

    CONST api = 'https://api.paris.fr/api/data/2.2/QueFaire/get_events/';

    /**
     * @Route("/paris", name="paris_api")
     */
    public function index()
    {
        $client = new Client();
        $res = $client->request('GET', self::api, [
            'query' => [
                'token' => getenv('EXTERNAL_PARIS_API_TOKEN'),
                'tags' => 7,
                'categories' => '',
                'end' => '',
                'offset' => '',
                'limit' => '',
                'start' => ''
            ]
        ]);

        $response = json_decode($res->getBody()->getContents());
        $data = $response->data;

        $events = [];

        foreach ($data as $d){
            $event = new Event();
            $event->setTitle($d->title);
            $event->setStart(new \DateTime($d->evenements->realDateStart));
            $event->setEnd(new \DateTime($d->evenements->realDateEnd));
            $events[] = $event;
        }

        usort($events, function($a, $b){
            if($a->getStart() > $b->getStart()){
                return 1;
            }
            if($a->getStart() < $b->getStart()){
                return -1;
            }
            return 0;
        });

        return $this->render('paris.html.twig',
            [
                'response' => $response,
                'events' => $events
            ]);
    }
}
