<?php


namespace App\Provider;


class ProviderManager
{
    /**
     * @var array|ProviderInterface
     */
    private $providers = [];

    /**
     * Importer constructor.
     * @param MeetupProvider $meetupProvider
     * @param OvsProvider $OvsProvider
     * @param CinemaProvider $cinemaProvider
     */
    public function __construct(
        MeetupProvider $meetupProvider,
        OvsProvider $OvsProvider,
        CinemaProvider $cinemaProvider
    ) {
        $this->providers[] = $meetupProvider;
        $this->providers[] = $OvsProvider;
        $this->providers[] = $cinemaProvider;
    }
    /**
     * @return array|ProviderInterface
     */
    public function getAll(): array {
        return $this->providers;
    }
}