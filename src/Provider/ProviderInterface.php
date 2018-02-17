<?php


namespace App\Provider;


use App\Entity\Event;

interface ProviderInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param \DateTime $day
     * @return array|Event
     */
    public function getEvents(\DateTime $day): array;
}