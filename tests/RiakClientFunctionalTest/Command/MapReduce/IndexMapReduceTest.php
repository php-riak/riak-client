<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

use Riak\Client\RiakOption;
use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Core\Query\Index\RiakIndexBin;
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
            $this->client->execute(new DeleteValue($location, []));
        }

        parent::tearDown();
    }

    private function setUpBucket()
    {
        $this->client->execute(StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($this->namespace)
            ->build());
    }

    private function setUpData()
    {
        for ($i = 0; $i < 100; $i++) {
            $this->storeObject($i, [$i,$i,$i], [
                (($i % 2) == 0) ? 'odd' : 'even',
                'number'
            ]);
        }
    }

    private function storeObject($key, $data, array $tags)
    {
        $json     = json_encode($data);
        $object   = new RiakObject($json, 'application/json');
        $location = new RiakLocation($this->namespace, $key);
        $command  = StoreValue::builder($location, $object)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $object->addIndex(new RiakIndexBin('tags', $tags));

        $this->client->execute($command);

        $this->locations[] = $location;
    }

    public function testIndexMapReduceMatch()
    {
        $source  = 'function(obj) { return obj; }';
        $map     = new AnonymousJsFunction($source);
        $reduce  = new ErlangFunction('riak_kv_mapreduce', 'reduce_identity');
        $command = IndexMapReduce::builder()
            ->withMapPhase($map)
            ->withReducePhase($reduce, null, true)
            ->withNamespace($this->namespace)
            ->withIndexBin('tags')
            ->withMatchValue('number')
            ->build();

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse', $result);
    }
}