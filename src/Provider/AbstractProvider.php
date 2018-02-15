<?php


namespace App\Provider;

use App\Util\Sanitizer;

abstract class AbstractProvider
{
    /**
     * @var Sanitizer
     */
    public $sanitizer;

    /**
     * MeetupProvider constructor.
     * @param Sanitizer $sanitizer
     */
    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }
}