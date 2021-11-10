<?php
namespace Modules\Klusbib\Api;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\MessageBag;
use Modules\Klusbib\Api\Exception\BadMethodCallException;
use Modules\Klusbib\Api\Exception\InvalidArgumentException;
use GuzzleHttp\ClientInterface;
use Modules\Klusbib\Api\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Client
{
    /**
     * The HTTP client instance used to communicate with API.
     *
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var
     */
    private $apiKey;

    /**
     * @var DateTime
     */
    private $apiKeyExpiration;

    /**
     * @var array
     */
    protected $messageBag;

    private $user;
    private $password;

    /**
     * API Client constructor.
     * @param $httpClient ClientInterface to call inventory
     * @param $apikey api key used for authentication at inventory
     * @param $user user for authentication at inventory (only to be provided to generate a new apikey)
     * @param $password password used for authentication at inventory (only to be provided to generate a new apikey)
     * @param SessionInterface $session used to preserve data accross http requests (e.g. apiKey)
     */
    public function __construct(ClientInterface $httpClient = null, $user = null, $password = null)
    {
        if ($httpClient != null) {
            $this->httpClient = $httpClient;
        } else {
            $this->httpClient = new \GuzzleHttp\Client([
                'base_uri' => config('klusbib.api_url'),
            ]);
        }
        $this->user = $user;
        $this->password = $password;
        $this->messageBag = array();
//        $this->apiKey = session('klusbib.apiKey');
//        $this->apiKeyExpiration = session('klusbib.apiKeyExpiration');
//        Log::debug('klusbib.apiKey from session:' . $this->apiKey . ' - expiration: ' . $this->apiKeyExpiration);
//            $this->updateToken();
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return ApiInterface
     */
    public function api($name)
    {
        switch ($name)
        {
            case 'users':
                $api = new Endpoints\Users($this);
                break;

            case 'enrolment':
                $api = new Endpoints\Enrolment($this);
                break;

            case 'lendings':
                $api = new Endpoints\Lendings($this);
                break;

             case 'reservations':
                $api = new Endpoints\Reservations($this);
                break;

             case 'deliveries':
                $api = new Endpoints\Deliveries($this);
                break;

            case 'payments':
                $api = new Endpoints\Payments($this);
                break;

            case 'membership':
                $api = new Endpoints\Membership($this);
                break;

            case 'token':
                $api = new Endpoints\Token($this);
                break;

            case 'stats':
                $api = new Endpoints\Stats($this);
                break;

            default:
                throw new InvalidArgumentException(sprintf('Undefined api instance called: "%s"', $name));
        }

        return $api;
    }

    /**
     * return an error array containing these elements
     * 'code'   : HTTP response code
     * 'errors' : array of errors. When a key is provided for an error, it matches the faulty field name
     * @return array errors from last request
     */
    public function errors() {
        return $this->messageBag;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return ApiInterface
     */
    public function __call($name, $args)
    {
        try {
            return $this->api($name);
        }
        catch (InvalidArgumentException $e) {
            throw new BadMethodCallException(sprintf('Undefined method called: "%s"', $name));
        }
    }

    public function updateToken(Session $session = null) {
        Log::debug('updateToken requested');
        // update apiKey from session
        if (isset($session) && $session->has('klusbib.apiKey')
            && $session->has('klusbib.apiKeyExpiration')) {
            if (!isset($this->apiKey)) {
                Log::debug('klusbib.apiKey retrieved from session:' . $this->apiKey);
                $this->apiKey = $session->get('klusbib.apiKey');
                $this->apiKeyExpiration = $session->get('klusbib.apiKeyExpiration');
            }
        }

        // check apiKey validity
        $now = new \DateTime('now');
        if (isset($this->apiKey) && isset($this->apiKeyExpiration)
            && $this->apiKeyExpiration > $now) {
            Log::debug('Reusing klusbib.apiKey from class');
            return;
        }

        // apiKey unavailable or expired -> generate new one
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'auth' => [
                $this->user,
                $this->password
            ],
//            "debug" => true
        ];
        Log::debug("Klusbib API request: POST; /token; (user:" . \json_encode($this->user)
            . "; options=" . \json_encode($options) . ")");
        try {
            $res = $this->httpClient->post('token', $options);
            Log::debug("Klusbib API token response=" . $res->getBody());
            $contentType = $this->getContentType($res);
            if (strpos($contentType, 'application/json') !== false ) {
                $body = \GuzzleHttp\json_decode($res->getBody());
                Log::debug("token: " . $body->token);
                $this->apiKey = $body->token;
                $this->apiKeyExpiration = new \DateTime('now');
                $this->apiKeyExpiration->add(new \DateInterval('PT2H'));;
                if (isset($session)) {
                    Log::debug('writing klusbib.apiKey to session');
                    $session->put('klusbib.apiKey', $this->apiKey);
                    $session->put('klusbib.apiKeyExpiration', $this->apiKeyExpiration);
                }
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
                throw new \Exception("Unable to access Klusbib API", null, $clientException);
            }
            throw new \Exception("Unexpected client exception!!", null, $clientException);
        }
    }
    // helper methods
    public function get($target) {
        return $this->request('GET', $target);
    }
    public function post($target, $data)
    {
        return $this->request('POST', $target, $data);
    }
    public function put($target, $data)
    {
        return $this->request('PUT', $target, $data);
    }
    public function patch($target, $data)
    {
        return $this->request('PATCH', $target, $data);
    }
    public function delete($target) {
        return $this->request('DELETE', $target);
    }
    private function request($method, $target, $data = null) {
        Log::info("Klusbib API request: $method; $target; " . json_encode($data));
        $this->messageBag = array(); // reset message bag

        if (!isset($this->apiKey)) {
            $this->updateToken();
        }
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
        Log::debug("Klusbib API request: $method; $target; options=" . json_encode($options));
        try {
            $time_start = microtime(true);

            $res = $this->httpClient->request($method, $target, $options);

            $time_end = microtime(true);
            $execution_time = ($time_end - $time_start);
            Log::debug("Klusbib API response: $method; $target; status=" . $res->getStatusCode() . "; duration=$execution_time secs; response=" . json_encode($res));

        } catch (ClientException $clientException) {
            Log::error( "Client exception - " . $clientException->getMessage());
            if ($clientException->hasResponse()) {

                $response = $clientException->getResponse();
                $statusCode = $response->getStatusCode();
                Arr::set($this->messageBag, 'code', $statusCode);
            }
            if (isset($statusCode) && ($statusCode == 401)) {
                Arr::set($this->messageBag, 'errors', array("Unauthorized (token expired?)"));
                // refresh api token
                $now = new \DateTime('now');
                $tokenAge =($this->apiKeyTimestamp) ? $now->diff($this->apiKeyTimestamp, true) : null;
                if ($this->apiKeyTimestamp == null || $tokenAge > new \DateInterval('P2H')) { // older than 2 hours
                    $this->updateToken();
                }
            }
            if (isset($statusCode) && ($statusCode == 404 || $statusCode == 403)) {
                Arr::set($this->messageBag, 'errors', array("Not found"));
                // access forbidden is considered as not found (can be an asset or user from another company)
                throw new NotFoundException();
            }
            else if (isset($statusCode) && ($statusCode == 400)) {
                $body = $response->getBody()->getContents();
                $msg = "Bad request" . " - " . $body;
                Arr::set($this->messageBag, 'errors', array($msg));
                Log::error($msg);
                return;
            }
            else if (isset($statusCode) && ($statusCode == 409)) {
                $body = $response->getBody()->getContents();
                $msg = "Conflict" . " - " . $body;
                Arr::set($this->messageBag, 'errors', array($msg));
                Log::error($msg);
                return;
            }
            else if (isset($statusCode) && ($statusCode >= 500)) {
                $msg = "Unable to access Klusbib API" . $clientException->getMessage();
                Arr::set($this->messageBag, 'errors', array($msg));
                Log::error( $msg);
                throw new \Exception("Unable to access Klusbib API", null, $clientException);
            }
            throw new \Exception("Unexpected client exception!!", null, $clientException);
        } catch (ServerException $serverException) {
            $msg = 'ServerException:' . $serverException->getMessage();
            Arr::set($this->messageBag, 'errors', array($msg));
            Log::error( $msg);
            throw new \Exception("Klusbib API unavailable", null, $serverException);
        } catch (\Exception $exception) {
            $msg = "Unexpected exception: " . $exception->getMessage();
            Arr::set($this->messageBag, 'errors', array($msg));
            Log::error( $msg);
            throw new \Exception("Unexpected exception!!", null, $exception);
        }

        if ($res != null && $res->getStatusCode() >= 400){
            if ($res->getStatusCode() == 404) {
                throw new NotFoundException();
            }
            $msg = 'Klusbib API request to "' . $target . '" failed with status code ' . $res->getStatusCode();
            Arr::set($this->messageBag, 'code', $res->getStatusCode());
            Arr::set($this->messageBag, 'errors', array($msg));
            Log::error($msg);
            throw new \RuntimeException('Klusbib API request to "' . $target . '" failed with status code ' . $res->getStatusCode());
        }
        Log::debug("Response body message=" . $res->getBody());
        $contentType = $this->getContentType($res);
        if (strpos($contentType, 'application/json') !== false ) {

            $decoded = \GuzzleHttp\json_decode($res->getBody(), true);
            if ($res->hasHeader('X-Total-Count')) {
                $result = array();
                $result['Total-Count'] = $res->getHeader('X-Total-Count')[0]; // pick value of first X-Total-Count header
                $result['items'] = $decoded;
                return $result;
            } else {
                return $decoded;
            }
        }
        return $res->getBody();
    }

    /**
     * @param $res
     * @return mixed
     */
    private function getContentType($res)
    {
        if ($res->hasHeader('content-type')) {
            return $res->getHeader('content-type')[0];
        }
        return 'text/HTML';
    }

}