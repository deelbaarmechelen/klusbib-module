<?php

namespace Modules\Klusbib\Api\Endpoints;


use Modules\Klusbib\Api\Client;

class Enrolment
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
     * Request enrolment.
     *
     * @param  array $params
     *
     * @return array
     */
    public function request(array $params)
    {
        return $this->client->post('enrolment', $params);
    }

}