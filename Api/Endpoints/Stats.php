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
    public function monthly($version = 1, $statMonth = null) {
        try {
            $target = 'stats/monthly';
            if ($version > 1) {
                $target .= "?version=$version";
                if (isset($statMonth)) {
                    $target.= "&stat-month=$statMonth";
                }
            }
            $statsResult = $this->client->get($target);
            \Log::debug('Klusbib Stats endpoint: monthly stats=' . \json_encode($statsResult));

            return $statsResult;
        } catch (NotFoundException $nfe) {
            return null;
        }

    }
}