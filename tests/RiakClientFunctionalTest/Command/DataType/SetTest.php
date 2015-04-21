<?php

namespace RiakClientFunctionalTest\Command\DataType;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\FetchSet;
use Riak\Client\Command\DataType\StoreSet;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class SetTest extends TestCase
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

        $namespace = new RiakNamespace('sets', 'sets');
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

    public function testStoreAndFetchSet()
    {
        $store = StoreSet::builder()
            ->withLocation($this->location)
            ->withReturnBody(true)
            ->add("Ottawa")
            ->add("Toronto")
            ->withPw(2)
            ->withDw(2)
            ->withW(3)
            ->build();

        $fetch = FetchSet::builder()
            ->withLocation($this->location)
            ->withNotFoundOk(true)
            ->withPr(1)
            ->withR(1)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);
        $set           = $fetchResponse->getDatatype();

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\StoreSetResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchSetResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakSet', $set);
        $this->assertEquals($this->location, $fetchResponse->getLocation());
        $this->assertEquals(["Ottawa","Toronto"], $set->getValue());
    }
}