<?php

namespace RiakClientFunctionalTest\Command\Bucket;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Command\Bucket\ListBuckets;
use Riak\Client\Command\Bucket\FetchBucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class BucketPropertiesTest extends TestCase
{
    public function testStoreAndFetchBucketProperties()
    {
        $namespace = new RiakNamespace('default', 'buckets');

        $store = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $fetch = FetchBucketProperties::builder()
            ->withNamespace($namespace)
            ->build();

        $storeResponse   = $this->client->execute($store);
        $fetchResponse   = $this->client->execute($fetch);
        $fetchProperties = $fetchResponse->getProperties();

        $this->assertInstanceOf('Riak\Client\Command\Bucket\Response\StoreBucketPropertiesResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\Bucket\Response\FetchBucketPropertiesResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\BucketProperties', $fetchProperties);
        $this->assertSame($namespace, $storeResponse->getNamespace());
        $this->assertSame($namespace, $fetchResponse->getNamespace());
        $this->assertTrue($fetchProperties->getAllowMult());
        $this->assertEquals(3, $fetchProperties->getNVal());
    }

    public function testListBuckets()
    {
        $command = ListBuckets::builder()
            ->withBucketType('default')
            ->build();

        $response = $this->client->execute($command);
        $iterator = $response->getIterator();
        $buckets  = $response->getBuckets();

        $this->assertInstanceOf('Riak\Client\Command\Bucket\Response\ListBucketsResponse', $response);
        $this->assertInstanceOf('Riak\Client\Command\Bucket\Response\ListBucketsIterator', $iterator);
        $this->assertInternalType('array', $buckets);
    }
}