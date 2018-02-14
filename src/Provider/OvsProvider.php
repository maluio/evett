<?php


namespace App\Provider;


use App\Entity\Event;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class OvsProvider
{
    CONST api = 'http://paris.onvasortir.com/vue_sortie_day.php';

    private $events = [];

    private $now;

    public function get()
    {
        $this->now = new \DateTime();

        $client = new Client();
        $response = $client->request('GET', self::api, [
            'query' => [
                'y' => $this->now->format('Y'),
                'm' => $this->now->format('m'),
                'd' => $this->now->format('d')
            ]
        ]);

        $html = $response->getBody()->getContents();

        $crawler = new Crawler($html);

        $crawler = $crawler->filter('#debutpage div.SiteColC div.Pad1Color2 > table > tr')->reduce(function (
            Crawler $tr,
            $i
        ) {
            return $i > 2;
        });

        /*foreach ($crawler as $domElement) {
            $html = $domElement->ownerDocument->saveHTML($domElement);
          //  dump($html);
        }*/

        $crawler->each(function (Crawler $tr, $i) {
            $time = $tr->filter('td')
                ->reduce(function ($td, $i) {
                    return $i == 1;
                });
            $time = str_split($time->text());
            $start = new \DateTime();
            $start->setTime(
                (int) $time[0].$time[1],
                (int) $time[3].$time[4]

            );
            $title = $tr->filter('td')
                ->reduce(function ($td, $i) {
                    return $i == 2;
                });
            $event = new Event();
            $event->setTitle($title->text());
            $event->setStart($start);
            $this->events[] = $event;
        });

        return $this->events;
    }
}