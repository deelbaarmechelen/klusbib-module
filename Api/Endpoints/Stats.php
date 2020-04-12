<?php

namespace Modules\Klusbib\Api\Endpoints;


use Modules\Klusbib\Api\Client;

class Stats
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
     * Monthly stats.
     *
     * @return array
     */
    public function monthly() {
        try {
            $statsResult = $this->client->get('stats/monthly');
            \Log::debug('Klusbib Stats endpoint: monthly stats=' . \json_encode($statsResult));

            return $statsResult;
        } catch (NotFoundException $nfe) {
            return null;
        }

    }
}