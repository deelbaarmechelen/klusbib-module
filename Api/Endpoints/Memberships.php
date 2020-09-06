<?php

namespace Modules\Klusbib\Api\Endpoints;

use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Api\Exception\NotFoundException;


class Memberships
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
     * Register a membership.
     *
     * @param  array $params
     *
     * @return array
     */
    public function create(array $params)
    {
        return $this->client->post('membership', $params);
    }

    /*
     * Update membership data
     *
     * @param  $user_id user id
     * @param  array $params
     *
     * @return object
     */
    public function update($memberships_id, array $params)
    {
        $excludedFields = array('created_at', 'updated_at');
        Log::debug("update of membership with id $memberships_id and params: " . \json_encode($params));
        $filteredParams = array_filter($params, function ($needle) use ($excludedFields) {
            return !in_array($needle, $excludedFields);
        }, ARRAY_FILTER_USE_KEY);

        Log::debug("update of membership with id $memberships_id and filteredParams: " . \json_encode($filteredParams));
        return $this->client->put('membership/'.rawurlencode($memberships_id), $filteredParams);
    }

    /**
     * Remove a membership by its id.
     *
     * @param  string $membership_id
     *
     * @return array
     */
    public function destroy($membership_id)
    {
        try {
            return $this->client->delete('membership/'.rawurlencode($membership_id));
        } catch (NotFoundException $nfe) {
            return null;
        }
    }

    /**
     * Get extended information about a membership by its id.
     *
     * @param  string $membership_id
     *
     * @return array
     */
    public function find($membership_id)
    {
        try {
            return $this->client->get('membership/'.rawurlencode($membership_id));
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
        $target = 'membership';
        if (count($params) > 0) {
            $target .= '?';
            foreach ($params as $key => $value) {
                if (substr($target, -1) != '?') {
                    $target .= '&';
                }
                $target .= $key . '=' . $value;
            }
        }
        $membershipsResult = $this->client->get($target);
        Log::debug('membership result' . \json_encode($membershipsResult));
        $result['items'] = $membershipsResult['items'];
        $totalCount = intval($membershipsResult['Total-Count']);
        $perPage = count($membershipsResult['items']) < 10 ? 10 : count($membershipsResult['items']);
        $result['pagination'] = array ('total' => $totalCount, 'perPage' => $perPage);
        Log::debug('pagination=' .\json_encode($result['pagination']));
        Log::debug('result' . \json_encode($result));
        return $result;
    }

}