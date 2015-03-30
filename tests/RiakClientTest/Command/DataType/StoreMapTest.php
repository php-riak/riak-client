<?php

namespace RiakClientTest\Command\DataType;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\StoreMap;
use Riak\Client\Command\DataType\MapUpdate;
use Riak\Client\Command\DataType\SetUpdate;
use Riak\Client\Command\DataType\CounterUpdate;

class StoreMapTest extends TestCase
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
        $counter = new CounterUpdate();
        $builder = StoreMap::builder($this->location, [])
            ->withLocation($this->location)
            ->withContext('context-hash')
            ->removeMap('map_key_remove')
            ->removeSet('set_key_remove')
            ->removeFlag('flag_key_remove')
            ->removeCounter('map_counter_remove')
            ->removeRegister('map_register_remove')
            ->updateMap('map_key', MapUpdate::create())
            ->updateSet('set_key', SetUpdate::create())
            ->updateRegister('map_register', 'foo')
            ->updateCounter('map_counter', 1)
            ->updateFlag('flag_key', true)
            ->withContext('context-hash')
            ->withReturnBody(true)
            ->withDw(1)
            ->withPw(2)
            ->withW(3);

        $counter->withDelta(1);
        $builder->updateCounter('other_counter', $counter);

        $this->assertInstanceOf('Riak\Client\Command\DataType\StoreMap', $builder->build());
    }
}