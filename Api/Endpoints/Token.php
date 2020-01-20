<?php

namespace Modules\Klusbib\Api\Endpoints;

use Modules\Klusbib\Api\Client;


class Token
{
    /**
     * The client.
     *
     * @var \Modules\Klusbib\Api\Client
     */
    protected $client;

    /**
     * @param \Modules\Klusbib\Api\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }


    /**
     * Create a token.
     *
     * @param  array $params
     *
     * @return array
     */
    public function add(array $params)
    {
        return $this->client->post('token', $params);
    }
}