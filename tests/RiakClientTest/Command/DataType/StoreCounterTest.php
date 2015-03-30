<?php

namespace RiakClientTest\Command\DataType;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakOption;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\StoreCounter;

class StoreCounterTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $location;

    /**
     * @var \Riak\Client\RiakClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\RiakTransport
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->location = new RiakLocation(new RiakNamespace('type', 'bucket'), 'key');
        $this->adapter  = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->node     = new RiakNode($this->adapter);
        $this->client   = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommand()
    {
        $builder = StoreCounter::builder($this->location, [])
            ->withLocation($this->location)
            ->withContext('context-hash')
            ->withReturnBody(true)
            ->withDelta(1)
            ->withDw(1)
            ->withPw(2)
            ->withW(3);

        $this->assertInstanceOf('Riak\Client\Command\DataType\StoreCounter', $builder->build());
    }
}