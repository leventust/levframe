<?php

namespace Core\Http;

use GuzzleHttp\Client as GuzzleClient;

class Client
{
    protected GuzzleClient $client;

    public function __construct()
    {
        $this->client = new GuzzleClient();
    }

    public function get($url, array $options = [])
    {
        return $this->client->get($url, $options);
    }

    public function post($url, array $options = [])
    {
        return $this->client->post($url, $options);
    }

    public function __call($method, $args)
    {
        return $this->client->$method(...$args);
    }
}
