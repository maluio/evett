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
     */
    public function __construct(
        MeetupProvider $meetupProvider,
        OvsProvider $OvsProvider
    ) {
        $this->providers[] = $meetupProvider;
        $this->providers[] = $OvsProvider;
    }
    /**
     * @return array|ProviderInterface
     */
    public function getAll(): array {
        return $this->providers;
    }
}