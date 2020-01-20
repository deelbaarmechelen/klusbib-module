<?php
use PHPUnit\Framework\TestCase;
use Modules\Klusbib\Models\Api\User;

class UserTest extends TestCase
{
    public function testFind()
    {
        \Illuminate\Support\Facades\Log::shouldReceive('error');
        \Illuminate\Support\Facades\Log::shouldReceive('info');
        \Illuminate\Support\Facades\Log::shouldReceive('debug');

        $client = new \Modules\Klusbib\Api\Client(
            new \GuzzleHttp\Client([
                'base_uri' => 'http://klusbibapi',
//            'base_uri' => 'http://localhost',
            ]),
            null, // api_key
            'snipe@klusbib.be', // user
            'snipe123' // password
        );
        \Torann\RemoteModel\Model::setClient($client);


        $apiUser = User::find(1);
        echo \json_encode($apiUser);
    }

}