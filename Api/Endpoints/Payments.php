<?php

namespace Modules\Klusbib\Api\Endpoints;

use Illuminate\Support\Facades\Log;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Api\Exception\NotFoundException;


class Payments
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
     * Get extended information about a payment by its id.
     *
     * @param  string $payment_id
     *
     * @return array
     */
    public function find($payment_id)
    {
        try {
            return $this->client->get('payments/'.rawurlencode($payment_id));
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
        $target = 'payments';
        if (count($params) > 0) {
            $target .= '?';
            foreach ($params as $key => $value) {
                if (substr($target, -1) != '?') {
                    $target .= '&';
                }
                $target .= $key . '=' . $value;
            }
        }
        $paymentsResult = $this->client->get($target);
        $result['items'] = $paymentsResult['items'];
        $totalCount = intval($paymentsResult['Total-Count']);
        $perPage = count($paymentsResult['items']) < 10 ? 10 : count($paymentsResult['items']);
        $result['pagination'] = array ('total' => $totalCount, 'perPage' => $perPage);
        Log::debug('pagination=' .\json_encode($result['pagination']));
        return $result;
    }

}