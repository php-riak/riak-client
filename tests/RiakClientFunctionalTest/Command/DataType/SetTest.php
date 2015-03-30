<?php

namespace RiakClientFunctionalTest\Command\DataType;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\RiakOption;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\FetchSet;
use Riak\Client\Command\DataType\StoreSet;
use Riak\Client\Core\Query\BucketProperties;
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
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($this->location)
            ->add("Ottawa")
            ->add("Toronto")
            ->build();

        $fetch = FetchSet::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($this->location)
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