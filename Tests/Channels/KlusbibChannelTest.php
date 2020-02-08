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
use Modules\Klusbib\Notifications\NotifyAssetCheckin;
use Modules\Klusbib\Notifications\NotifyAssetCheckout;
use PHPUnit\Framework\TestCase;
use Torann\RemoteModel\Model;

class KlusbibChannelTest extends TestCase
{
    private $container;

    public function testSendCheckout() {
        \Illuminate\Support\Facades\Log::shouldReceive('error');
        \Illuminate\Support\Facades\Log::shouldReceive('info');
        \Illuminate\Support\Facades\Log::shouldReceive('debug');

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

        $client = new Client($this->createGuzzleClientMock($mock));
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
        $this->assertEquals('{"user_id":1,"tool_id":2,"tool_type":"TOOL","start_date":"","due_date":null,"comments":"note text"}',
            $this->container[1]['request']->getBody()->read(100));
    }

    public function testSendCheckin() {
        \Illuminate\Support\Facades\Log::shouldReceive('error');
        \Illuminate\Support\Facades\Log::shouldReceive('info');
        \Illuminate\Support\Facades\Log::shouldReceive('debug');

        $mock = new MockHandler([
            new Response(201, ['Content-Type' => 'application/json', 'X-Foo' => 'Bar'],
                '{"status": "ok", "token": "123.456.789-000"}'), // POST token
            new Response(200, ['Content-Type' => 'application/json', 'X-Total-Count' => 1], // Get Lending by user, tool, startDate
                '[{
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
                }]'),
            new Response(200, ['Content-Type' => 'application/json'], // PUT lending
                '{
                    "lending_id": 1,
                    "start_date": "2020-02-01",
                    "due_date": "2020-02-08",
                    "returned_date": "2020-02-05",
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
        ]);

        $client = new Client($this->createGuzzleClientMock($mock));
        Model::setClient($client);
        $channel = new KlusbibChannel();
        $notifiable = null;
        $user = new User();
        $user->employee_num = 1;

        $asset = new Asset();
        $asset->id = 2;
        $asset->asset_tag = 'KB-000-20-001';
        $params = array(
            'target' => $user,
            'item' => $asset,
            'admin' => true,
            'log_id' => 123,
            'target_type' => 'api',
            'settings' => '',
            'note' => 'note text'
        );
        $notification = new NotifyAssetCheckin($params);
        $channel->send($notifiable, $notification);
//        $this->assertEquals(3, count($this->container));
//        echo $this->container[0]['request']->
        $this->assertEquals('POST', $this->container[0]['request']->getMethod()); // create token
        $this->assertEquals('token', $this->container[0]['request']->getUri()->getPath());
        $this->assertEquals('GET', $this->container[1]['request']->getMethod()); // create lending
        $this->assertEquals('lendings', $this->container[1]['request']->getUri()->getPath());
        $this->assertEquals('',
            $this->container[1]['request']->getBody()->read(100));
        $this->assertEquals('PUT', $this->container[2]['request']->getMethod()); // create lending
        $this->assertEquals('lendings/1', $this->container[2]['request']->getUri()->getPath());
        $this->assertEquals('{"lending_id":1,"start_date":"2020-02-01","due_date":"2020-02-08","returned_date":',
            $this->container[2]['request']->getBody()->read(82)); // don't check return date as it changes every day (current date)
    }
    private function createGuzzleClientMock(MockHandler $mockHandler) {
        $this->container = [];
        $history = Middleware::history($this->container);

        $handlerStack = HandlerStack::create($mockHandler);
        // Add the history middleware to the handler stack.
        $handlerStack->push($history);

        return new \GuzzleHttp\Client(['handler' => $handlerStack]);
    }
}
