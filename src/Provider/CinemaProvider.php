<?php


namespace App\Provider;


use App\Entity\Event;
use Symfony\Component\DomCrawler\Crawler;

class CinemaProvider extends AbstractProvider implements ProviderInterface
{

    /**
     * @var string
     */
    protected CONST key = 'CINEMA';

    /**
     * @var Event[]
     */
    protected $events = [];

    /**
     * @param \DateTime $day
     * @return Event[]
     */
    public function getEvents(\DateTime $day): array
    {
        $this->events = [];
        // Forum des Images
        $fdi = 'http://www.allocine.fr/seance/salle_gen_csalle=C0119.html';
        $this->getEventsForTheatre($day, $fdi, 'Forum des images');

        // Cinematheque
        $sf = 'http://www.allocine.fr/seance/salle_gen_csalle=C1559.html';
        $this->getEventsForTheatre($day, $sf, 'La Cinémathèque française');

        // Le Grand Action
        $ga = 'http://www.allocine.fr/seance/salle_gen_csalle=C0072.html';
        $this->getEventsForTheatre($day, $ga,'Le Grand Action');

        return $this->events;
    }

    protected function getEventsForTheatre(\DateTime $day, string $theatreUrl, string $theatreName): void
    {
        $response = $this->httpClient->request('GET', $theatreUrl);

        $html = $response->getBody()->getContents();
        $crawler = new Crawler();
        $crawler->addHtmlContent($html);


        $crawler->filter('.js-movie-list')->each(function ($node, $i) use ($day, $theatreUrl, $theatreName){
            $data = json_decode($node->attr('data-movies-showtimes'),true);
            $formatedDay = $day->format('Y-m-d');

            foreach ($data['showtimes'] as $cinema){
                if(!isset($cinema[$formatedDay])){
                    break;
                }
                $movie = $cinema[$formatedDay];
                foreach ($movie as $movieId => $content){
                    // the date part is just random to make the url unique
                    $movieUrl = 'http://www.allocine.fr/film/fichefilm_gen_cfilm=' . $movieId . '.html?=date' . $formatedDay;
                    $start = $content[0]['showtimes'][0]['showStart'];
                    $title = $data['movies'][$movieId]['title'];
                    $event = new Event();
                    $event->setTitle($title . $this->getMovieInfo($movieUrl));
                    $event->setStart(new \DateTime($start));
                    $event->setProvider($this->getKey());
                    $event->setUrl($movieUrl);
                    $event->setDescription($theatreName);
                    $this->events[] = $event;
                }
            }
        });
    }

    protected function getMovieInfo(string $movieUrl): string {
        $response = $this->httpClient->request('GET', $movieUrl);

        $html = $response->getBody()->getContents();
        $crawler = new Crawler();
        $crawler->addHtmlContent($html);

       $director =  $crawler->filter('span[itemprop="director"] span[itemprop="name"]')->each(function ($node){
            return $node->text();
        });

       $director = isset($director[0]) ? $director[0] : 'unkown';

        return ' [' . $director . ']' ;

    }

}