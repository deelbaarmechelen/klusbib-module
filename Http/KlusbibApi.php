<?php
namespace Modules\Klusbib\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class KlusbibApi
{
    public static function instance() {
        // Create default HandlerStack

//        return new KlusbibApi(new \GuzzleHttp\Client([
//            'base_uri' => config('klusbib.api_url'),
//        ]),config('klusbib.api_key'));
        return new KlusbibApi(new \GuzzleHttp\Client([
            'base_uri' => config('klusbib.api_url'),
        ]),null,config('klusbib.api_user'), config('klusbib.api_pwd'));
    }

    private $client;
    private $apiKey;
    private $apiKeyTimestamp;
    private $user;
    private $password;

    /**
     * InventoryImpl constructor.
     * @param $client ClientInterface to call inventory
     * @param $apikey api key used for authentication at inventory
     */
    public function __construct(ClientInterface $client, $apiKey = null, $user = null, $password = null)
    {
        $this->client = $client;
        if (isset($apiKey)) {
            $this->apiKey = $apiKey;
            $this->apiKeyTimestamp = new \DateTime('now');
        } else {
            $this->user = $user;
            $this->password = $password;
            $response = $this->getToken();
        }
    }

    public function getUsers() {
        return $this->get('/users');
    }

    public function getItems() {
        return $this->get('/tools');
    }

    private function getToken() {
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth' => [
                $this->user,
                $this->password
            ]
        ];
        Log::debug("Klusbib API request: POST; /token; (user:" . $this->user . ")");
        try {
            $res = $this->client->request('POST', '/token', $options);
            $contentType = $res->getHeader('content-type')[0];
            Log::debug("Klusbib API token response=" . $res->getBody());
            if (strpos($contentType, 'application/json') !== false ) {
                $body = \GuzzleHttp\json_decode($res->getBody());
                Log::debug("token: " . $body->token);
                $this->apiKey = $body->token;
                $this->apiKeyTimestamp = new \DateTime('now');
                return $this->apiKey;
            }
            return $res->getBody();

        } catch (ClientException $clientException) {
            if ($clientException->hasResponse()) {
                $response = $clientException->getResponse();
                $statusCode = $response->getStatusCode();
            }
            if (isset($statusCode) && ($statusCode == 404 || $statusCode == 403)) {
                // access forbidden is considered as not found (can be an asset or user from another company)
                throw new NotFoundException();
            }
            else if (isset($statusCode) && ($statusCode >= 500)) {
                throw new KlusbibApiException("Unable to access Klusbib API", null, $clientException);
            }
            throw new KlusbibApiException("Unexpected client exception!!", null, $clientException);
        }
    }
    // helper methods
    private function get($target) {
        return $this->request('GET', $target);
    }

    private function post($target, $data)
    {
        return $this->request('POST', $target, $data);
    }

    private function put($target, $data)
    {
        return $this->request('PUT', $target, $data);
    }
    private function patch($target, $data)
    {
        return $this->request('PATCH', $target, $data);
    }
    private function delete($target) {
        return $this->request('DELETE', $target);
    }
    private function request($method, $target, $data = null) {
        Log::error("Klusbib API request: $method; $target; " . json_encode($data));
        $res = null;
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ],
        ];
        if (isset($data)) {
            $options[RequestOptions::JSON] = $data;
        }
//        Log::error("Klusbib API request: $method; $target; options=" . json_encode($options));
        Log::info("Klusbib API request: $method; $target; " . json_encode($data));
        try {
            $time_start = microtime(true);

            $res = $this->client->request($method, $target, $options);

            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);
            Log::info("Inventory request duration: $execution_time secs");

        } catch (ClientException $clientException) {
            if ($clientException->hasResponse()) {
                $response = $clientException->getResponse();
                $statusCode = $response->getStatusCode();
            }
            if (isset($statusCode) && ($statusCode == 401)) {
                // refresh api token
                $now = new \DateTime('now');
                $tokenAge =($this->apiKeyTimestamp) ? $now->diff($this->apiKeyTimestamp, true) : null;
                if ($this->apiKeyTimestamp == null || $tokenAge > new \DateInterval('P2H')) { // older than 2 hours
                    $this->getToken();
                }
            }

            if (isset($statusCode) && ($statusCode == 404 || $statusCode == 403)) {
                // access forbidden is considered as not found (can be an asset or user from another company)
                throw new NotFoundException();
            }
            else if (isset($statusCode) && ($statusCode >= 500)) {
                throw new KlusbibApiException("Unable to access Klusbib API", null, $clientException);
            }
            throw new KlusbibApiException("Unexpected client exception!!", null, $clientException);
        } catch (ServerException $serverException) {
            throw new KlusbibApiException("Klusbib API unavailable", null, $serverException);
        } catch (\Exception $exception) {
            throw new KlusbibApiException("Unexpected exception!!", null, $exception);
        }

        if ($res != null && $res->getStatusCode() >= 400){
            if ($res->getStatusCode() == 404) {
                throw new NotFoundException();
            }
            Log::error('Klusbib API request to "' . $target . '" failed with status code ' . $res->getStatusCode());
            throw new \RuntimeException('Klusbib API request to "' . $target . '" failed with status code ' . $res->getStatusCode());
        }
        $contentType = $res->getHeader('content-type')[0];
        Log::debug("Response body message=" . $res->getBody());
        if (strpos($contentType, 'application/json') !== false ) {
            return \GuzzleHttp\json_decode($res->getBody());
        }
        return $res->getBody();
    }

}