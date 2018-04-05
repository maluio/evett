<?php


namespace App\Provider;


use App\Entity\Event;

interface ProviderInterface
{
    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @param \DateTime $day
     * @return Event[]
     */
    public function getEvents(\DateTime $day): array;
}