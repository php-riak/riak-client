<?php

namespace RiakClientFunctionalTest\Command\Bucket;

use Riak\Client\RiakOption;
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

    public function testEncodeAndDecodeQuorum()
    {
        $namespace = new RiakNamespace('default', 'bucket_quorum');

        $store = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::N_VAL, RiakOption::QUORUM)
            ->withProperty(BucketProperties::PR, RiakOption::ALL)
            ->withProperty(BucketProperties::R, RiakOption::ALL)
            ->withProperty(BucketProperties::W, RiakOption::ONE)
            ->withProperty(BucketProperties::PW, RiakOption::ONE)
            ->withProperty(BucketProperties::DW, 'all')
            ->withProperty(BucketProperties::RW, 'one')
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
        $this->assertEquals('quorum', $fetchProperties->getNVal());
        $this->assertEquals('all', $fetchProperties->getPr());
        $this->assertEquals('all', $fetchProperties->getR());
        $this->assertEquals('one', $fetchProperties->getW());
        $this->assertEquals('one', $fetchProperties->getPw());
        $this->assertEquals('all', $fetchProperties->getDw());
        $this->assertEquals('one', $fetchProperties->getRw());
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