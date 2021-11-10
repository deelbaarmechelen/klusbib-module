<?php

namespace Modules\Klusbib\Api\Endpoints;


use Illuminate\Support\Facades\Log;
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

    /**
     * Confirm a pending enrolment.
     *
     * @param  array $params
     *
     * @return boolean true if no errors occured
     */
    public function confirm(array $params)
    {
        try {
            $this->client->post('enrolment_confirm', $params); // response expected to be empty
            return empty($this->client->errors());
        } catch (\Exception $ex) {
            return false;
        }
    }

    /**
     * Decline a pending enrolment.
     *
     * @param  array $params
     *
     * @return boolean true if no errors occured
     */
    public function decline(array $params)
    {
        try {
            $this->client->post('enrolment_decline', $params); // response expected to be empty
            return empty($this->client->errors());
        } catch (\Exception $ex) {
            return false;
        }
    }
}