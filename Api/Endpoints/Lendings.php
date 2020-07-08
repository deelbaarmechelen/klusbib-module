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

    public function findActiveByUserTool($userId, $toolId, $toolType) {
        try {
            $lendingsResult = $this->client->get('lendings?user_id='.rawurlencode($userId).'&tool_id='. rawurlencode($toolId)
                .'&tool_type='. rawurlencode($toolType). '&active=true&_sortDir=asc');

            if (count($lendingsResult['items']) == 0) {
                return null;
            } else if (count($lendingsResult['items']) > 1 && $toolType == 'TOOL') {
                Log::error('duplicate active lendings for user ' . $userId . '; tool ' . $toolId . '; tool_type='.$toolType);
                return null;
            }
            return $lendingsResult['items'][0];
        } catch (NotFoundException $nfe) {
            return null;
        }

    }
    public function findByUserToolStart($userId, $toolId, $toolType, $startDate) {
        try {
            $lendingsResult = $this->client->get('lendings?user_id='.rawurlencode($userId).'&tool_id='. rawurlencode($toolId)
                .'&tool_type='. rawurlencode($toolType). '&start_date='. rawurlencode($startDate) . '&_sortDir=asc');

            if (count($lendingsResult['items']) == 0) {
                return null;
            } else if (count($lendingsResult['items']) > 1 && $toolType == 'TOOL') {
                Log::error('duplicate lendings for user ' . $userId . '; tool ' . $toolId . '; tool_type='.$toolType
                    . '; start_date=' . $startDate);
                return null;
            }
            return $lendingsResult['items'][0];
        } catch (NotFoundException $nfe) {
            return null;
        }

    }

    /**
     * Get all lendings.
     *
     * @param array $params query parameters
     *
     * @return array
     */
    public function all(array $params = [])
    {
        $result = array();
        // API call returns an array
        $target = 'lendings';
        if (count($params) > 0) {
            $target .= '?';
            foreach ($params as $key => $value) {
                if (substr($target, -1) != '?') {
                    $target .= '&';
                }
                $target .= $key . '=' . $value;
            }
        }
        $lendingsResult = $this->client->get($target);
        $result['items'] = $lendingsResult['items'];
        $totalCount = intval($lendingsResult['Total-Count']);
        $perPage = count($lendingsResult['items']) < 10 ? 10 : count($lendingsResult['items']);
        $result['pagination'] = array ('total' => $totalCount, 'perPage' => $perPage);
        Log::debug('pagination=' .\json_encode($result['pagination']));
        return $result;
    }

}