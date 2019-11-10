<?php

namespace Modules\Klusbib\Console;

use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Auth;

//require_once __DIR__ . '/../../../vendor/autoload.php';

class UpdateAvatar extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'klusbib:update-avatar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update users avatar based on membership status.';

    protected $client;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get list of users and current status from API
        // see https://laravel-json-api.readthedocs.io/en/latest/features/http-clients/
        // lookup corresponding user
        // update avatar of user: green if active, red if expired, orange if expired less than x days?
        // copy code from ProfileController? postIndex(ImageUploadRequest $request) ?
        /** @var \CloudCreativity\LaravelJsonApi\Contracts\Client\ClientInterface $client */
//        $client = json_api()->client('http://api.klusbib.be/');
//        new \GuzzleHttp\Client([
//            'base_uri' => INVENTORY_URL . '/api/v1/',
//            'handler' => $stack
//        ])
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'http://api.klusbib.be/'
        ]);
        $this->client = $client;
        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $client->query('tools');
        print_r($response->getStatusCode());
        print_r($response->getBody());
        var_dump($response->getBody());
        $json = $this->get('/tools', null);
        print_r($json);
        $user = User::find(4);
        // avatar should be placed in public/uploads/avatar directory
//            $user->avatar = "DBM_avatar_nok.png";
//            $user->avatar = "DBM_avatar_ok.png";
//        }
        if ($user->save()) {
            echo "success!\n";
        }
    }

    private function get($target, $data)
    {
        return $this->request('GET', $target, $data);
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
        $options = [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
//                'Authorization' => 'Bearer '.$this->apiKey,
            ],
        ];
        if (isset($data)) {
            $options[RequestOptions::JSON] = $data;
        }
//        $this->logger->info("Klusbib request: $method; $target; " . json_encode($data));
        try {
            $res = $this->client->request($method, $target, $options);

        } catch (ClientException $clientException) {
            if ($clientException->hasResponse()) {
                $response = $clientException->getResponse();
                $statusCode = $response->getStatusCode();
            }
            if (isset($statusCode) && ($statusCode == 404 || $statusCode == 403)) {
                // access forbidden is considered as not found (can be an asset or user from another company)
//                throw new \Api\Exception\NotFoundException();
            }
            else if (isset($statusCode) && ($statusCode >= 500)) {
//                throw new \Api\Exception\InventoryException("Unable to access inventory", null, $clientException);
            }

        } catch (ServerException $serverException) {
//            throw new \Api\Exception\InventoryException("Inventory unavailable", null, $serverException);
        }

        if ($res->getStatusCode() >= 400){
            if ($res->getStatusCode() == 404) {
//                throw new \Api\Exception\NotFoundException();
            }
//            $this->logger->error('Inventory request to "' . $target . '" failed with status code ' . $res->getStatusCode());
            throw new \RuntimeException('Inventory request to "' . $target . '" failed with status code ' . $res->getStatusCode());
        }
        $contentType = $res->getHeader('content-type')[0];
//        $this->logger->debug("Response body message=" . $res->getBody());
        if (strpos($contentType, 'application/json') !== false ) {
            return \GuzzleHttp\json_decode($res->getBody());
        }
        return $res->getBody();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
//            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
