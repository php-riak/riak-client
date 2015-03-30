<?php

namespace RiakClientFunctionalTest\Command\DataType;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\RiakOption;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Command\DataType\FetchCounter;
use Riak\Client\Command\DataType\StoreCounter;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class CounterTest extends TestCase
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    protected $location;

    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('counters', 'counters');
        $command   = StoreBucketProperties::builder()
            ->withAllowMulti(true)
            ->withNVal(3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($command);

        $this->key      = uniqid();
        $this->location = new RiakLocation($namespace, $this->key);
    }

    protected function tearDown()
    {
        if ($this->client) {
            $this->client->execute(DeleteValue::builder($this->location)
                ->build());
        }

        parent::tearDown();
    }

    public function testStoreAndFetchCounter()
    {

        $store = StoreCounter::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($this->location)
            ->withDelta(10)
            ->build();

        $fetch = FetchCounter::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($this->location)
            ->build();

        $fetchResponse1 = $this->client->execute($fetch);
        $storeResponse  = $this->client->execute($store);
        $fetchResponse2 = $this->client->execute($fetch);

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchCounterResponse', $fetchResponse1);
        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\StoreCounterResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchCounterResponse', $fetchResponse2);

        $this->assertNull($fetchResponse1->getDatatype());
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakCounter', $storeResponse->getDatatype());
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakCounter', $fetchResponse2->getDatatype());

        $this->assertEquals($this->location, $fetchResponse2->getLocation());
        $this->assertEquals(10, $storeResponse->getDatatype()->getValue());
        $this->assertEquals(10, $fetchResponse2->getDatatype()->getValue());
    }
}