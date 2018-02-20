<?php


namespace App\Provider;

use App\Util\HttpClient;
use App\Util\Sanitizer;

abstract class AbstractProvider
{
    /**
     * @var Sanitizer
     */
    protected $sanitizer;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    /**
     * AbstractProvider constructor.
     * @param Sanitizer $sanitizer
     * @param HttpClient $httpClient
     */
    public function __construct(Sanitizer $sanitizer, HttpClient $httpClient)
    {
        $this->sanitizer = $sanitizer;
        $this->httpClient = $httpClient;
    }
}