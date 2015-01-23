<?php

namespace RiakClientFunctionalTest\Command\DataType;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Cap\RiakOption;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\FetchCounter;
use Riak\Client\Command\DataType\StoreCounter;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class CounterTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client->execute(StoreBucketProperties::builder()
            ->withNamespace(new RiakNamespace('counters', 'counters'))
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->build());
    }

    public function testStoreAndFetchCounter()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('counters', 'counters'), $key);

        $store = StoreCounter::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->withDelta(10)
            ->build();

        $fetch = FetchCounter::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
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

        $this->assertEquals($location, $fetchResponse2->getLocation());
        $this->assertEquals(10, $storeResponse->getDatatype()->getValue());
        $this->assertEquals(10, $fetchResponse2->getDatatype()->getValue());
    }
}