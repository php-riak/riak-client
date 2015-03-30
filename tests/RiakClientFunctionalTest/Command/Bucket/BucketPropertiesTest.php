<?php

namespace RiakClientFunctionalTest\Command\Bucket;

use Riak\Client\RiakOption;
use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Bucket\ListBuckets;
use Riak\Client\Command\Bucket\FetchBucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class BucketPropertiesTest extends TestCase
{
    public function testStoreAndFetchBucketProperties()
    {
        $namespace = new RiakNamespace('default', 'buckets');

        $store = StoreBucketProperties::builder()
            ->withNamespace($namespace)
            ->withAllowMulti(true)
            ->withNVal(3)
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
            ->withNamespace($namespace)
            ->withR(RiakOption::QUORUM)
            ->withPr(RiakOption::ONE)
            ->withPW(RiakOption::ONE)
            ->withW(RiakOption::ONE)
            ->withDw('all')
            ->withRW('one')
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
        $this->assertEquals('quorum', $fetchProperties->getR());
        $this->assertEquals('one', $fetchProperties->getW());
        $this->assertEquals('one', $fetchProperties->getPw());
        $this->assertEquals('one', $fetchProperties->getPr());
        $this->assertEquals('all', $fetchProperties->getDw());
        $this->assertEquals('one', $fetchProperties->getRw());
    }

    public function testConfigureBucketFunctions()
    {
        $namespace = new RiakNamespace(null, 'bucket_func');

        $store = StoreBucketProperties::builder()
            ->withNamespace($namespace)
            ->withAllowMulti(true)
            ->build();

        $fetch = FetchBucketProperties::builder()
            ->withNamespace($namespace)
            ->build();

        $storeResponse   = $this->client->execute($store);
        $fetchResponse   = $this->client->execute($fetch);
        $fetchProperties = $fetchResponse->getProperties();

        $this->assertInstanceOf('Riak\Client\Core\Query\BucketProperties', $fetchProperties);
        $this->assertInstanceOf('Riak\Client\Command\Bucket\Response\StoreBucketPropertiesResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\Bucket\Response\FetchBucketPropertiesResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\ErlangFunction', $fetchProperties->getChashKeyFunction());
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\ErlangFunction', $fetchProperties->getLinkwalkFunction());
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