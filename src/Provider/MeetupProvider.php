<?php


namespace App\Provider;


use App\Entity\Event;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class MeetupProvider
{

    CONST api = 'https://www.meetup.com/fr-FR/find/events/';

    private $events = [];

    private $day;

    public function get($day)
    {
        $this->day = $day;

        $client = new Client();
        $response = $client->request('GET', self::api, [
            'query' => [
                'allMeetups' => 'true',
                'radius' => '26',
                'userFreeform' => 'Paris',
                'mcId' => 'c1011740',
                'mcName' => 'Paris%2C+FR',
                'year' => $this->day->format('Y'),
                'month' => $this->day->format('m'),
                'day' => $this->day->format('d'),
            ]
        ]);

        $html = $response->getBody()->getContents();

        $crawler = new Crawler();
        $crawler->addHtmlContent($html);
        // $crawler = $crawler->filter('ul.searchResults');
        $year = $this->day->format('Y');
        $month = $this->day->format('n');
        $day = $this->day->format('j');
        $class = '.container-' . $year . '-' . $month . '-' . $day;

        $crawler = $crawler->filter('ul.searchResults ' . $class . ' li')->each(function (Crawler $el, $i) {
            // dump($el->text());
            $time = $el->filter('.row-item a')->reduce(function ($el, $i) {
                return $i == 0;
            });
            $time = preg_replace("/[^ \w]+/", "", $time->text());
            $time = str_split($time);
            $start = clone $this->day;
            if(count($time)===4){
                $start->setTime(
                    (int) $time[0].$time[1],
                    (int) $time[2].$time[3]
                );
            }
            else{
                $start->setTime(
                    (int) $time[0],
                    (int) $time[1].$time[1]
                );
            }

            $group = $el
                ->filter('.row-item a')->reduce(function ($el, $i) {
                    return $i == 1;
                });
            $group = preg_replace("/[^ \w]+/", "", $group->text());

            $title = $el
                ->filter('.row-item a')->reduce(function ($el, $i) {
                    return $i == 2;
                });
            $url = $title->attr('href');
            $title = preg_replace("/[^ \w]+/", "", $title->text());

            $event = new Event();
            $event->setTitle($title);
            $event->setUrl($url);
            $event->setDescription($group);
            $event->setStart($start);
            $event->setProvider('MEETUP');
            $this->events[] = $event;
        });

        /*        foreach ($crawler as $domElement) {
                    $html = $domElement->ownerDocument->saveHTML($domElement);
                   dump($html);
                }*/
        //dump($html);


        return $this->events;
    }
}