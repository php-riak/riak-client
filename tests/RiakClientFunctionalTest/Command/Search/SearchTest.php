<?php

namespace RiakClientFunctionalTest\Command\Search;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Search\Search;
use Riak\Client\Core\Query\RiakNamespace;
use RiakClientFixture\Data\Search\ThunderCatsData;

abstract class SearchTest extends TestCase
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
        $bucket    = sprintf('test_riak_client_%s_cats', $hash);
        $index     = sprintf('test_riak_client_%s_famous', $hash);
        $namespace = new RiakNamespace('default', $bucket);
        $data      = new ThunderCatsData($this->client, $namespace, $index);

        $this->searchData = $data;
        $this->indexName  = $index;
        $this->namespace  = $namespace;

        $this->searchData->setUp();
    }

    public function testSimpleSearch()
    {
        $this->searchData->storeThunderCats();

        $index       = $this->indexName;
        $searchSnarf = Search::builder()
            ->withQuery('name_s:Snarf')
            ->withIndex($index)
            ->build();

        $searchSnarfResult = $this->client->execute($searchSnarf);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\SearchResponse', $searchSnarfResult);

        $numResults = $searchSnarfResult->getNumResults();
        $results    = $searchSnarfResult->getSingleResults();

        $this->assertCount(1, $results);
        $this->assertEquals(1, $numResults);
        $this->assertArrayHasKey('name_s', $results[0]);
        $this->assertEquals('Snarf', $results[0]['name_s']);
    }

    public function testSearchReturnFields()
    {
        $this->searchData->storeThunderCats();

        $index  = $this->indexName;
        $search = Search::builder()
            ->withReturnFields(['name_s', 'age_i'])
            ->withQuery('age_i:30')
            ->withIndex($index)
            ->withNumRows(10)
            ->build();

        $searchResult = $this->client->execute($search);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\SearchResponse', $searchResult);

        $numResults = $searchResult->getNumResults();
        $results    = $searchResult->getSingleResults();

        usort($results, function($arg1, $arg2) {
            return strcmp($arg2['name_s'], $arg1['name_s']);
        });

        $this->assertCount(2, $results);
        $this->assertEquals(2, $numResults);
        $this->assertCount(2, $results[0]);
        $this->assertCount(2, $results[1]);
        $this->assertEquals(30, $results[0]['age_i']);
        $this->assertEquals(30, $results[1]['age_i']);
        $this->assertEquals('Lion-o', $results[0]['name_s']);
        $this->assertEquals('Cheetara', $results[1]['name_s']);
    }
}