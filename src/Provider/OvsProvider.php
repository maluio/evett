<?php


namespace App\Provider;


use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class OvsProvider
{
    CONST api = 'http://paris.onvasortir.com/vue_sortie_day.php';

    private $events = [];

    private $day;

    private $em;

    private $eventRepository;

    public function __construct(EntityManagerInterface $em, EventRepository $eventRepository)
    {
        $this->em = $em;
        $this->eventRepository = $eventRepository;
    }

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

            if($this->eventRepository->urlExists($url)){
                return;
            }

            $event = new Event();
            $event->setTitle($title->text());
            $event->setUrl($url);
            $event->setProvider('OVS');
            $event->setStart($start);
            $this->events[] = $event;
        });

        foreach ($this->events as $event){
            $this->em->persist($event);
        }

        $this->em->flush();

        return $this->events;
    }
}