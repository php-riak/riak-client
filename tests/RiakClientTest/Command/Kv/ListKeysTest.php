<?php

namespace RiakClientTest\Command\Kv;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Kv\ListKeys;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Message\Kv\ListKeysResponse;

class ListKeysTest extends TestCase
{
    private $namespace;
    private $client;
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->adapter   = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->namespace = new RiakNamespace('type', 'bucket');
        $this->node      = new RiakNode($this->adapter);
        $this->client    = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testListKeys()
    {
        $response = new ListKeysResponse();
        $command  = ListKeys::builder()
            ->withNamespace($this->namespace)
            ->withTimeout(90)
            ->build();

        $response->iterator = new \ArrayIterator([
            new \ArrayIterator(['key1', 'key2']),
            new \ArrayIterator(['key3', 'key4'])
        ]);

        $this->adapter->expects($this->once())
            ->method('send')
            ->will($this->returnValue($response));

        $result   = $this->client->execute($command);
        $iterator = $result->getIterator();

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\ListKeysResponse', $result);
        $this->assertInstanceOf('Iterator', $iterator);
    }
}