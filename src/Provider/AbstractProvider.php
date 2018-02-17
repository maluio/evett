<?php


namespace App\Provider;

use App\Util\Sanitizer;
use GuzzleHttp\Client;

abstract class AbstractProvider
{
    /**
     * @var Sanitizer
     */
    public $sanitizer;

    /**
     * @var Client
     */
    public $httpClient;

    /**
     * AbstractProvider constructor.
     * @param Sanitizer $sanitizer
     */
    public function __construct(Sanitizer $sanitizer)
    {
        $this->sanitizer = $sanitizer;
        $this->httpClient = new Client();
    }
}