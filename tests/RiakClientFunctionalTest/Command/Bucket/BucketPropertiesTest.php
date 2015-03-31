<?php

namespace RiakClientFunctionalTest\Command\Bucket;

use Riak\Client\RiakOption;
use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Bucket\ListBuckets;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Core\Query\Func\NamedJsFunction;
use Riak\Client\Command\Bucket\FetchBucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class BucketPropertiesTest extends TestCase
{
    public function testStoreAndFetchBucketProperties()
    {
        $namespace = new RiakNamespace('default', 'buckets');

        $store = StoreBucketProperties::builder()
            ->withNamespace($namespace)
            ->withLastWriteWins(false)
            ->withBasicQuorum(false)
            ->withNotFoundOk(true)
            ->withAllowMulti(true)
            ->withOldVClock(86400)
            ->withSmallVClock(50)
            ->withYoungVClock(20)
            ->withBigVClock(50)
            ->withNVal(3)
            ->withRw(3)
            ->withDw(2)
            ->withPr(1)
            ->withPw(2)
            ->withW(1)
            ->withR(3)
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
        $store     = StoreBucketProperties::builder($namespace)
            ->withLinkwalkFunction(new ErlangFunction('riak_kv_wm_link_walker', 'mapreduce_linkfun'))
            ->withChashkeyFunction(new ErlangFunction('riak_core_util', 'chash_std_keyfun'))
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
        $this->assertEquals('riak_kv_wm_link_walker', $fetchProperties->getLinkwalkFunction()->getModule());
        $this->assertEquals('mapreduce_linkfun', $fetchProperties->getLinkwalkFunction()->getFunction());
        $this->assertEquals('chash_std_keyfun', $fetchProperties->getChashKeyFunction()->getFunction());
        $this->assertEquals('riak_core_util', $fetchProperties->getChashKeyFunction()->getModule());
    }

    public function testConfigureCommitHooks()
    {
        $namespace = new RiakNamespace(null, 'bucket_hooks');
        $store     = StoreBucketProperties::builder($namespace)
            ->withPostcommitHook(new NamedJsFunction('Riak.mapValuesJson'))
            ->withPrecommitHook(new ErlangFunction('riak_kv_mapreduce', 'map_object_value'))
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

        $postcommitHooks = $fetchProperties->getPostcommitHooks();
        $precommitHooks  = $fetchProperties->getPrecommitHooks();

        $this->assertCount(1, $precommitHooks);
        $this->assertCount(1, $postcommitHooks);
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\NamedJsFunction', $precommitHooks[0]);
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\ErlangFunction', $postcommitHooks[0]);
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