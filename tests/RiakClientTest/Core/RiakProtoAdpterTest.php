<?php

namespace RiakClientTest\Core;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Kv\GetRequest;
use Riak\Client\Core\Message\Kv\PutRequest;
use Riak\Client\Core\Message\Kv\DeleteRequest;
use Riak\Client\Core\RiakProtoAdpter;

class RiakProtoAdpterTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Adapter\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\RiakProtoAdpter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Adapter\Proto\ProtoClient', [], [], '', false);
        $this->instance = new RiakProtoAdpter($this->client);
    }

    public function testCreateAdapterStrategy()
    {
        $kvGet    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new GetRequest()]);
        $kvPut    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new PutRequest()]);
        $kvDelete = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new DeleteRequest()]);

        $this->assertInstanceOf('Riak\Client\Core\Adapter\Proto\Kv\ProtoGet', $kvGet);
        $this->assertInstanceOf('Riak\Client\Core\Adapter\Proto\Kv\ProtoPut', $kvPut);
        $this->assertInstanceOf('Riak\Client\Core\Adapter\Proto\Kv\ProtoDelete', $kvDelete);
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