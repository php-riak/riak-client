<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\Index\RiakIndexBin;
use Riak\Client\Core\Query\Index\RiakIndexInt;
use Riak\Client\Command\MapReduce\IndexMapReduce;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Core\Query\Func\AnonymousJsFunction;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class IndexMapReduceTest extends TestCase
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
        $bucket    = sprintf('test_riak_client_%s_index_mapreduce', $hash);
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
        for ($i = 0; $i < 100; $i++) {
            $links = [$i % 10];
            $tags  =  [
                (($i % 2) == 0) ? 'even' : 'odd',
                'number'
            ];

            $this->storeObject($i, $i, $tags, $links);
        }
    }

    private function storeObject($key, $data, array $tags, array $links)
    {
        $json     = json_encode($data);
        $object   = new RiakObject($json, 'application/json');
        $location = new RiakLocation($this->namespace, $key);
        $command  = StoreValue::builder($location, $object)
            ->withPw(1)
            ->withW(2)
            ->build();

        $object->addIndex(new RiakIndexBin('tags', $tags));
        $object->addIndex(new RiakIndexInt('links', $links));

        $this->client->execute($command);

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

    public function testIndexMapReduceMatch()
    {
        $map     = $this->createMapFunction();
        $reduce  = new ErlangFunction('riak_kv_mapreduce', 'reduce_sum');
        $command = IndexMapReduce::builder()
            ->withMapPhase($map)
            ->withReducePhase($reduce, null, true)
            ->withNamespace($this->namespace)
            ->withIndexBin('tags')
            ->withMatchValue('even')
            ->build();

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse', $result);

        $iterator = $result->getIterator();
        $values   = iterator_to_array($iterator);

        $this->assertCount(1, $values);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\MapReduceEntry', $values[0]);
        $this->assertEquals(1, $values[0]->getPhase());
        $this->assertEquals([2450], $values[0]->getResponse());
    }

    public function testIndexIntMapReduceMatch()
    {
        $map     = $this->createMapFunction();
        $reduce  = new ErlangFunction('riak_kv_mapreduce', 'reduce_sum');
        $command = IndexMapReduce::builder()
            ->withMapPhase($map)
            ->withReducePhase($reduce, null, true)
            ->withNamespace($this->namespace)
            ->withIndexInt('links')
            ->withRange(1, 2)
            ->build();

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse', $result);

        $results = $result->getResults();
        $values  = $result->getResultsFromAllPhases();

        $this->assertCount(1, $values);
        $this->assertCount(1, $results);
        $this->assertEquals([930], $values);
        $this->assertEquals([ 1 => [930]], $results);
    }

    public function testIndexMapReduceKeepMap()
    {
        $map     = $this->createMapFunction();
        $command = IndexMapReduce::builder()
            ->withMapPhase($map, null, true)
            ->withNamespace($this->namespace)
            ->withIndexBin('tags')
            ->withMatchValue('odd')
            ->build();

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse', $result);

        $phaseZeroResults = $result->getResultForPhase(0);
        $allResults       = $result->getResultsFromAllPhases();
        $expected         = [1,3,5,7,9,11,13,15,17,19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,55,57,59,61,63,65,67,69,71,73,75,77,79,81,83,85,87,89,91,93,95,97,99];

        $this->assertInternalType('array', $phaseZeroResults);
        $this->assertCount(50, $phaseZeroResults);

        sort($phaseZeroResults);
        sort($allResults);

        $this->assertEquals($expected, $phaseZeroResults);
        $this->assertEquals($expected, $allResults);
    }
}