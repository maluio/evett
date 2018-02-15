<?php


namespace App\Provider;


use App\Entity\Event;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class OvsProvider
{
    CONST api = 'http://paris.onvasortir.com/vue_sortie_day.php';

    private $events = [];

    private $day;

    public function get($day)
    {
        $this->day = $day;

        $client = new Client();
        $response = $client->request('GET', self::api, [
            'query' => [
                'y' => $this->day->format('Y'),
                'm' => $this->day->format('m'),
                'd' => $this->day->format('d')
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

        $crawler->each(function (Crawler $tr, $i) {
            $time = $tr->filter('td')
                ->reduce(function ($td, $i) {
                    return $i == 1;
                });
            $time = str_split($time->text());
            $start = clone $this->day;
            $start->setTime(
                (int) $time[0].$time[1],
                (int) $time[3].$time[4]

            );
            $title = $tr->filter('td')
                ->reduce(function ($td, $i) {
                    return $i == 2;
                });

            $url = 'http://paris.onvasortir.com/' . $title->filter('a')->attr('href');

            $event = new Event();
            $event->setTitle($title->text());
            $event->setUrl($url);
            $event->setProvider('OVS');
            $event->setStart($start);
            $this->events[] = $event;
        });

        return $this->events;
    }
}