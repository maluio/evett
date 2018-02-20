<?php


namespace App\Util;


use GuzzleHttp\Client;

class HttpClient
{

    /**
     * @var Client
     */
    private $guzzleClient;

    /**
     * HttpClient constructor.
     * @param Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    public function  request($method, $uri = '', array $options = []){
        return $this->guzzleClient->request($method, $uri, $options);
    }

}