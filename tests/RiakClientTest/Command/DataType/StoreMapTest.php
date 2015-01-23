<?php

namespace RiakClientTest\Command\DataType;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\Cap\RiakOption;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\StoreMap;
use Riak\Client\Command\DataType\StoreSet;
use Riak\Client\Command\DataType\MapUpdate;
use Riak\Client\Command\DataType\SetUpdate;

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
     * @var \Riak\Client\Core\RiakAdapter
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->location = new RiakLocation(new RiakNamespace('bucket', 'type'), 'key');
        $this->adapter  = $this->getMock('Riak\Client\Core\RiakAdapter');
        $this->node     = new RiakNode($this->adapter);
        $this->client   = $builder
            ->withNode($this->node)
            ->build();
    }


    public function testBuildCommand()
    {
        $command = StoreMap::builder()
            ->withOption(RiakOption::N_VAL, 1)
            ->withLocation($this->location)
            ->updateMap('map_key', MapUpdate::create())
            ->updateSet('set_key', SetUpdate::create())
            ->updateRegister('map_register', 'foo')
            ->updateCounter('map_counter', 1)
            ->updateFlag('flag_key', true)
            ->build();

        $command
            ->removeMap('map_key')
            ->removeSet('set_key')
            ->removeFlag('flag_key')
            ->removeCounter('map_counter')
            ->removeRegister('map_register');

        $this->assertInstanceOf('Riak\Client\Command\DataType\StoreMap', $command);
    }
}