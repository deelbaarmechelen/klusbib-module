<?php

namespace Modules\Klusbib\Api\Endpoints;

use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Api\Exception\NotFoundException;


class Users
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
     * Register a user.
     *
     * @param  array $params
     *
     * @return array
     */
    public function add(array $params)
    {
        return $this->client->post('users', $params);
    }

    /*
     * Update user data
     *
     * @param  $user_id user id
     * @param  array $params
     *
     * @return object
     */
    public function update($user_id, array $params)
    {
        $excludedFields = array('user_id', 'reservations', 'created_at', 'updated_at');
        Log::debug("update of user with id $user_id and params: " . \json_encode($params));
        $filteredParams = array_filter($params, function ($needle) use ($excludedFields) {
            return !in_array($needle, $excludedFields);
        }, ARRAY_FILTER_USE_KEY);

        Log::debug("update of user with id $user_id and filteredParams: " . \json_encode($filteredParams));
        return $this->client->put('users/'.rawurlencode($user_id), $filteredParams);
    }

    /**
     * Get extended information about a user by its id.
     *
     * @param  string $user_id
     *
     * @return array
     */
    public function find($user_id)
    {
        try {
            return $this->client->get('users/'.rawurlencode($user_id));
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
        $target = 'users';
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