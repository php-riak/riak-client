<?php

namespace RiakClientTest\Command\DataType;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\Cap\RiakOption;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\Crdt\RiakSet;
use Riak\Client\Core\Query\Crdt\RiakCounter;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\StoreSet;

class StoreSetTest extends TestCase
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

        $this->location = new RiakLocation(new RiakNamespace('bucket', 'type'), 'key');
        $this->adapter  = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->node     = new RiakNode($this->adapter);
        $this->client   = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommand()
    {
        $builder = StoreSet::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->add(new RiakCounter(1))
            ->remove(new RiakCounter(1))
            ->add(new RiakCounter(2))
            ->build();

        $this->assertInstanceOf('Riak\Client\Command\DataType\StoreSet', $builder);
    }
}