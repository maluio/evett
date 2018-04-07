<?php


namespace App\Provider;

use App\Entity\Event;
use Symfony\Component\DomCrawler\Crawler;

class OvsProvider extends AbstractProvider implements ProviderInterface
{
    private CONST api = 'http://paris.onvasortir.com/vue_sortie_day.php';

    protected CONST key = 'OVS';

    private $events = [];

    private $day;

    public function getEvents(\DateTime $day): array
    {
        $this->events = [];

        $this->day = $day;

        $response = $this->httpClient->request('GET', self::api, [
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
            $title = $this->sanitizer->removeUndesiredCharacters($title->text());
            $event->setTitle($title);
            $event->setUrl($url);
            $event->setProvider($this->getKey());
            $event->setStart($start);
            $this->events[] = $event;
        });

        return $this->events;
    }
}