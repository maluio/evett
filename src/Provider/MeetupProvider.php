<?php


namespace App\Provider;

use App\Entity\Event;
use Symfony\Component\DomCrawler\Crawler;

class MeetupProvider extends AbstractProvider implements ProviderInterface
{

    private CONST api = 'https://www.meetup.com/fr-FR/find/events/';

    private CONST name = 'MEETUP';

    /**
     * @var array
     */
    private $events = [];

    /**
     * @var \DateTime
     */
    private $day;

    public function getName(): string
    {
        return self::name;
    }

    /**
     * @param $day
     * @return array[Events]
     */
    public function getEvents(\DateTime $day) : array
    {
        $this->day = $day;

        $response = $this->httpClient->request('GET', self::api, [
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
        $year = $this->day->format('Y');
        $month = $this->day->format('n');
        $day = $this->day->format('j');
        $class = '.container-' . $year . '-' . $month . '-' . $day;

        $crawler = $crawler->filter('ul.searchResults ' . $class . ' li')->each(function (Crawler $el, $i) {
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
            $group = $this->sanitizer->removeUndesiredCharacters($group->text());

            $title = $el
                ->filter('.row-item a')->reduce(function ($el, $i) {
                    return $i == 2;
                });
            $url = $title->attr('href');

            $title = $this->sanitizer->removeUndesiredCharacters($title->text());

            $event = new Event();
            $event->setTitle($title);
            $event->setUrl($url);
            $event->setDescription($group);
            $event->setStart($start);
            $event->setProvider($this->getName());
            $this->events[] = $event;
        });

        return $this->events;
    }
}