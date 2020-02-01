<?php

namespace Modules\Klusbib\Api\Endpoints;

use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Api\Exception\NotFoundException;

class Lendings
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
     * Register a lending.
     *
     * @param  array $params
     *
     * @return array
     */
    public function create(array $params)
    {
        return $this->client->post('lendings', $params);
    }

    /*
     * Update user data
     *
     * @param  $lendingId lending id
     * @param  array $params
     *
     * @return object
     */
    public function update($lendingId, array $params)
    {
        $excludedFields = array('user_id', 'tool_id', 'created_at', 'updated_at');
        Log::debug("update of lending with id $lendingId and params: " . \json_encode($params));
        $filteredParams = array_filter($params, function ($needle) use ($excludedFields) {
            return !in_array($needle, $excludedFields);
        }, ARRAY_FILTER_USE_KEY);

        Log::debug("update of lending with id $lendingId and filteredParams: " . \json_encode($filteredParams));
        return $this->client->put('lendings/'.rawurlencode($lendingId), $filteredParams);
    }

    /**
     * Get extended information about a lending by its id.
     *
     * @param  string $lendingId
     *
     * @return array
     */
    public function find($lendingId)
    {
        try {
            return $this->client->get('lendings/'.rawurlencode($lendingId));
        } catch (NotFoundException $nfe) {
            return null;
        }
    }

}