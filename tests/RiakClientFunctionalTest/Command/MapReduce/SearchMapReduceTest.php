<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\SearchMapReduce;
use RiakClientFixture\Data\Search\ThunderCatsData;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Core\Query\Func\AnonymousJsFunction;

abstract class SearchMapReduceTest extends TestCase
{
    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    /**
     * @var \RiakClientFixture\Data\Search\ThunderCatsData
     */
    private $searchData;

    protected function setUp()
    {
        parent::setUp();

        $hash      = hash('crc32', __CLASS__ );
        $bucket    = sprintf('test_riak_client_%s_search_mapreduce_bucket', $hash);
        $index     = sprintf('test_riak_client_%s_search_mapreduce_index', $hash);
        $namespace = new RiakNamespace('thunder_cats', $bucket);
        $data      = new ThunderCatsData($this->client, $namespace, $index);

        $this->searchData = $data;
        $this->indexName  = $index;
        $this->namespace  = $namespace;

        $this->searchData->setUp();
        $this->searchData->storeThunderCats();
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

        return [JSON.parse(value.values[i].data).name_s];
    }

    return [];
}');
    }

    public function testSearchMapReduceMatch()
    {
        $index   = $this->indexName;
        $map     = $this->createMapFunction();
        $reduce  = new ErlangFunction('riak_kv_mapreduce', 'reduce_sort');
        $command = SearchMapReduce::builder()
            ->withMapPhase($map)
            ->withReducePhase($reduce, null, true)
            ->withQuery('name_s:Snarf')
            ->withIndex($index)
            ->build();

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\SearchMapReduceResponse', $result);

        $iterator = $result->getIterator();
        $values   = iterator_to_array($iterator);

        $this->assertCount(1, $values);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Response\MapReduceEntry', $values[0]);
        $this->assertEquals(["Snarf"], $values[0]->getResponse());
        $this->assertEquals(1, $values[0]->getPhase());
    }
}