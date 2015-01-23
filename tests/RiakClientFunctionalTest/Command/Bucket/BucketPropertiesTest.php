<?php

namespace RiakClientFunctionalTest\Command\Bucket;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Command\Bucket\FetchBucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class BucketPropertiesTest extends TestCase
{
    public function testStoreAndFetchBucketProperties()
    {
        $namespace = new RiakNamespace('bucket', 'default');

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
        $this->assertTrue($fetchProperties->getAllowSiblings());
        $this->assertEquals(3, $fetchProperties->getNVal());
    }
}