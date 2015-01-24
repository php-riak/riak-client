<?php

namespace RiakClientTest\Core;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakHttpTransport;
use Riak\Client\Core\Message\Kv\GetRequest;
use Riak\Client\Core\Message\Kv\PutRequest;
use Riak\Client\Core\Message\Kv\DeleteRequest;

class RiakHttpAdpterTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\RiakHttpTransport
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new RiakHttpTransport($this->client);
    }

    public function testCreateAdapterStrategy()
    {
        $get    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new GetRequest()]);
        $put    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new PutRequest()]);
        $delete = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new DeleteRequest()]);

        $this->assertInstanceOf('Riak\Client\Core\Transport\Http\Kv\HttpGet', $get);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Http\Kv\HttpPut', $put);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Http\Kv\HttpDelete', $delete);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnknownMessageException()
    {
        $mock = $this->getMock('Riak\Client\Core\Message\Request');

        $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [$mock]);
    }
}