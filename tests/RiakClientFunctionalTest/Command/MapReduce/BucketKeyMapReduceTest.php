<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Core\Query\Func\AnonymousJsFunction;
use Riak\Client\Command\Bucket\StoreBucketProperties;
use Riak\Client\Command\MapReduce\BucketKeyMapReduce;

abstract class BucketKeyMapReduceTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation[]
     */
    protected $locations;

    protected function setUp()
    {
        parent::setUp();

        $hash      = hash('crc32', __CLASS__ );
        $bucket    = sprintf('test_riak_client_%s_bucket_key_mapreduce', $hash);
        $namespace = new RiakNamespace(null, $bucket);

        $this->namespace = $namespace;

        $this->setUpBucket();
        $this->setUpData();
    }

    protected function tearDown()
    {
        foreach ($this->locations as $location) {
            $this->client->execute(new DeleteValue($location));
        }

        parent::tearDown();
    }

    private function setUpBucket()
    {
        $this->client->execute(StoreBucketProperties::builder()
            ->withNamespace($this->namespace)
            ->withAllowMulti(true)
            ->withNVal(3)
            ->build());
    }

    private function setUpData()
    {
        for ($i = 0; $i < 20; $i++) {
            $this->storeObject($i, $i);
        }
    }

    private function storeObject($key, $data)
    {
        $json     = json_encode($data);
        $object   = new RiakObject($json, 'application/json');
        $location = new RiakLocation($this->namespace, $key);

        $this->client->execute(StoreValue::builder($location, $object)
            ->withPw(1)
            ->withW(2)
            ->build());

        $this->locations[] = $location;
    }

    /**
     * @return \Riak\Client\Core\Query\Func\RiakFunction
     */
    public function createMapFunction()
    {
        return new AnonymousJsFunction('
function(value) {

    for (i = 0; i < value.values.length; i++) {
        if (value.values[i].metadata["X-Riak-Deleted"]) {
            continue;
        }

        return [JSON.parse(value.values[i].data)];
    }

    return [];
}');
    }

    public function testBucketKeyMapReduce()
    {
        $map     = $this->createMapFunction();
        $reduce  = new ErlangFunction('riak_kv_mapreduce', 'reduce_sum');
        $command = BucketKeyMapReduce::builder([])
            ->withMapPhase($map, null, false)
            ->withReducePhase($reduce, null, true)
            ->withLocation($this->locations[0])
            ->withLocation($this->locations[1])
            ->withLocation($this->locations[2])
            ->build();

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\BucketKeyMapReduceResponse', $result);

        $iterator = $result->getIterator();
        $values   = iterator_to_array($iterator);

        $this->assertCount(1, $values);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\MapReduceEntry', $values[0]);
        $this->assertEquals([3], $values[0]->getResponse());
        $this->assertEquals(1, $values[0]->getPhase());
    }
}