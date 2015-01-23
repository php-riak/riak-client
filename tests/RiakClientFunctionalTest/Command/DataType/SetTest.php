<?php

namespace RiakClientFunctionalTest\Command\DataType;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Cap\RiakOption;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\FetchSet;
use Riak\Client\Command\DataType\StoreSet;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class SetTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('sets', 'sets');
        $store     = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);
    }

    public function testStoreAndFetchSet()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('sets', 'sets'), $key);

        $store = StoreSet::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->add("Ottawa")
            ->add("Toronto")
            ->build();

        $fetch = FetchSet::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);
        $set           = $fetchResponse->getDatatype();

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\StoreSetResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchSetResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakSet', $set);
        $this->assertEquals($location, $fetchResponse->getLocation());
        $this->assertEquals(["Ottawa","Toronto"], $set->getValue());
    }
}