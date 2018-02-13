<?php


namespace App\Provider;

use GuzzleHttp\Client;
use App\Entity\Event;


class ParisApiProvider
{
    CONST api = 'https://api.paris.fr/api/data/2.2/QueFaire/get_events/';

    public function get(){
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

        return $events;
    }

}