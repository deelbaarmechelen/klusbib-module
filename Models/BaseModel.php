<?php


namespace Modules\Klusbib\Models;

use Modules\Klusbib\Api\Client;
use Torann\RemoteModel\Model;

class BaseModel extends Model
{
    /**
     * Make request through API.
     *
     * @return mixed
     */
    protected function request($endpoint = null, $method, $params)
    {
        $endpoint = $endpoint ? $endpoint : $this->endpoint;

        $results = Client::$method($endpoint, $params);

        return $results ? $this->newInstance($results) : null;
    }
}