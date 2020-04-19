<?php

namespace Modules\Klusbib\Api\Endpoints;

use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Api\Exception\NotFoundException;


class Reservations
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
     * Register a reservation.
     *
     * @param  array $params
     *
     * @return array
     */
    public function create(array $params)
    {
        return $this->client->post('reservations', $params);
    }

    /*
     * Update reservation data
     *
     * @param  $user_id user id
     * @param  array $params
     *
     * @return object
     */
    public function update($reservation_id, array $params)
    {
        $excludedFields = array('created_at', 'updated_at');
        Log::debug("update of reservation with id $reservation_id and params: " . \json_encode($params));
        $filteredParams = array_filter($params, function ($needle) use ($excludedFields) {
            return !in_array($needle, $excludedFields);
        }, ARRAY_FILTER_USE_KEY);

        Log::debug("update of reservation with id $reservation_id and filteredParams: " . \json_encode($filteredParams));
        return $this->client->put('reservations/'.rawurlencode($reservation_id), $filteredParams);
    }

    /**
     * Remove a reservation by its id.
     *
     * @param  string $reservation_id
     *
     * @return array
     */
    public function destroy($reservation_id)
    {
        try {
            return $this->client->delete('reservations/'.rawurlencode($reservation_id));
        } catch (NotFoundException $nfe) {
            return null;
        }
    }

    /**
     * Get extended information about a reservation by its id.
     *
     * @param  string $reservation_id
     *
     * @return array
     */
    public function find($reservation_id)
    {
        try {
            return $this->client->get('reservations/'.rawurlencode($reservation_id));
        } catch (NotFoundException $nfe) {
            return null;
        }
    }

    /**
     * Get all users.
     *
     * @param array $params query parameters
     *
     * @return array
     */
    public function all(array $params = [])
    {
        $result = array();
        // API call returns an array
        $target = 'reservations';
        if (count($params) > 0) {
            $target .= '?';
            foreach ($params as $key => $value) {
                if (substr($target, -1) != '?') {
                    $target .= '&';
                }
                $target .= $key . '=' . $value;
            }
        }
        $usersResult = $this->client->get($target);
        $result['items'] = $usersResult['items'];
        $totalCount = intval($usersResult['Total-Count']);
        $perPage = count($usersResult['items']);
        $result['pagination'] = array ('total' => $totalCount, 'perPage' => $perPage);
        Log::debug('pagination=' .\json_encode($result['pagination']));
        return $result;
    }

}