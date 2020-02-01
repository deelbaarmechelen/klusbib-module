<?php

namespace Modules\Klusbib\Channels;

use App\Models\Asset;
use App\Models\User;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Middleware;
use Modules\Klusbib\Api\Client;
use Modules\Klusbib\Notifications\NotifyAssetCheckout;
use PHPUnit\Framework\TestCase;
use Torann\RemoteModel\Model;

class KlusbibChannelTest extends TestCase
{
    private $container;

    public function testSend() {
        \Illuminate\Support\Facades\Log::shouldReceive('error');
        \Illuminate\Support\Facades\Log::shouldReceive('info');
        \Illuminate\Support\Facades\Log::shouldReceive('debug');

        $client = new Client($this->createGuzzleClientMock());
        Model::setClient($client);
        $channel = new KlusbibChannel();
        $notifiable = null;
        $user = new User();
        $user->employee_num = 1;

        $asset = new Asset();
        $asset->id = 2;
        $asset->asset_tag = 'KB-000-20-001';
//        $asset->last_checkout = new \Datetime(); // date cannot be set without real database connection?
        $params = array(
            'target' => $user,
            'item' => $asset,
            'admin' => true,
            'log_id' => 123,
            'target_type' => 'api',
            'settings' => '',
            'note' => 'note text'
        );
        $notification = new NotifyAssetCheckout($params);
        $channel->send($notifiable, $notification);
        $this->assertEquals(2, count($this->container));
//        echo $this->container[0]['request']->
        $this->assertEquals('POST', $this->container[0]['request']->getMethod()); // create token
        $this->assertEquals('token', $this->container[0]['request']->getUri()->getPath());
        $this->assertEquals('POST', $this->container[1]['request']->getMethod()); // create lending
        $this->assertEquals('lendings', $this->container[1]['request']->getUri()->getPath());
        $this->assertEquals('{"user_id":1,"tool_id":2,"start_date":"","due_date":null,"comments":"note text"}',
            $this->container[1]['request']->getBody()->read(100));
    }

    private function createGuzzleClientMock() {
        $this->container = [];
        $history = Middleware::history($this->container);

        $mock = new MockHandler([
            new Response(201, ['Content-Type' => 'application/json', 'X-Foo' => 'Bar'],
                '{"status": "ok", "token": "123.456.789-000"}'),
            new Response(200, ['Content-Type' => 'application/json'],
                '{
                    "lending_id": 1,
                    "start_date": "2020-02-01",
                    "due_date": "2020-02-08",
                    "returned_date": null,
                    "tool_id": "1",
                    "user_id": "1",
                    "comments": "test",
                    "active": null,
                    "created_by": "tester",
                    "created_at": {
                        "date": "2020-01-29 22:15:33.000000",
                        "timezone_type": 3,
                        "timezone": "Europe/Berlin"
                    },
                    "updated_at": {
                        "date": "2020-01-29 22:15:33.000000",
                        "timezone_type": 3,
                        "timezone": "Europe/Berlin"
                    }
                }'),
            new RequestException('Error Communicating with Server', new Request('GET', 'test'))
        ]);

        $handlerStack = HandlerStack::create($mock);
        // Add the history middleware to the handler stack.
        $handlerStack->push($history);

        return new \GuzzleHttp\Client(['handler' => $handlerStack]);
    }
}
