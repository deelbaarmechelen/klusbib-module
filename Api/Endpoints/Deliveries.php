<?php

namespace Modules\Klusbib\Api\Endpoints;

use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Api\Exception\NotFoundException;


class Deliveries
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
     * Register a delivery.
     *
     * @param  array $params
     *
     * @return array
     */
    public function create(array $params)
    {
        return $this->client->post('deliveries', $params);
    }

    /*
     * Update delivery data
     *
     * @param  $user_id user id
     * @param  array $params
     *
     * @return object
     */
    public function update($delivery_id, array $params)
    {
        $excludedFields = array('created_at', 'updated_at');
        Log::debug("update of delivery with id $delivery_id and params: " . \json_encode($params));
        $filteredParams = array_filter($params, function ($needle) use ($excludedFields) {
            return !in_array($needle, $excludedFields);
        }, ARRAY_FILTER_USE_KEY);

        Log::debug("update of delivery with id $delivery_id and filteredParams: " . \json_encode($filteredParams));
        return $this->client->put('deliveries/'.rawurlencode($delivery_id), $filteredParams);
    }

    /**
     * Remove a delivery by its id.
     *
     * @param  string $delivery_id
     *
     * @return array
     */
    public function destroy($delivery_id)
    {
        try {
            return $this->client->delete('deliveries/'.rawurlencode($delivery_id));
        } catch (NotFoundException $nfe) {
            return null;
        }
    }

    /**
     * Get extended information about a delivery by its id.
     *
     * @param  string $delivery_id
     *
     * @return array
     */
    public function find($delivery_id)
    {
        try {
            return $this->client->get('deliveries/'.rawurlencode($delivery_id));
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
        $target = 'deliveries';
        if (count($params) > 0) {
            $target .= '?';
            foreach ($params as $key => $value) {
                if (substr($target, -1) != '?') {
                    $target .= '&';
                }
                $target .= $key . '=' . $value;
            }
        }
        $deliveriesResult = $this->client->get($target);
        $result['items'] = $deliveriesResult['items'];
        $totalCount = intval($deliveriesResult['Total-Count']);
        $perPage = count($deliveriesResult['items']) < 10 ? 10 : count($deliveriesResult['items']);
        $result['pagination'] = array ('total' => $totalCount, 'perPage' => $perPage);
        Log::debug('pagination=' .\json_encode($result['pagination']));
        return $result;
    }

    public function addItem($delivery_id, $item_id) {
        $data = array("item_id" => $item_id);
        return $this->client->post('deliveries/'.rawurlencode($delivery_id).'/items', $data);
    }

    public function removeItem($delivery_id, $item_id) {
        try {
            return $this->client->delete('deliveries/'.rawurlencode($delivery_id).'/items/'.rawurlencode($item_id));
        } catch (NotFoundException $nfe) {
            return null;
        }

    }
}