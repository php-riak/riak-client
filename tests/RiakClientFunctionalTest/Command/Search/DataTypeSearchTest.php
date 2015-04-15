<?php

namespace RiakClientFunctionalTest\Command\Search;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Search\Search;
use Riak\Client\Core\Query\RiakNamespace;
use RiakClientFixture\Data\Search\ArticlesData;

abstract class DataTypeSearchTest extends TestCase
{
    /**
     * @var string
     */
    private $indexCrdt;

    /**
     * @var string
     */
    private $indexKv;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespaceCrdt;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespaceKv;

    /**
     * @var \RiakClientFixture\Data\Search\ArticlesData
     */
    private $dataCrdt;

    /**
     * @var \RiakClientFixture\Data\Search\ArticlesData
     */
    private $dataKv;

    protected function setUp()
    {
        parent::setUp();

        $hash          = hash('crc32', __CLASS__ );
        $bucketCrdt    = sprintf('test_riak_client_%s_crdt_articles_bucket', $hash);
        $indexCrdt     = sprintf('test_riak_client_%s_crdt_articles_index', $hash);
        $bucketKv      = sprintf('test_riak_client_%s_kv_articles_bucket', $hash);
        $indexKv       = sprintf('test_riak_client_%s_kv_articles_index', $hash);
        $namespaceCrdt = new RiakNamespace('maps', $bucketCrdt);
        $namespaceKv   = new RiakNamespace(null, $bucketKv);
        $dataCrdt      = new ArticlesData($this->client, $namespaceCrdt, $indexCrdt);
        $dataKv        = new ArticlesData($this->client, $namespaceKv, $indexKv);

        $this->dataKv        = $dataKv;
        $this->dataCrdt      = $dataCrdt;
        $this->indexKv       = $indexKv;
        $this->indexCrdt     = $indexCrdt;
        $this->namespaceKv   = $namespaceKv;
        $this->namespaceCrdt = $namespaceCrdt;

        $this->dataCrdt->setUp();
        $this->dataKv->setUp();
    }

    public function testSimpleCrdtSearch()
    {
        $this->dataCrdt->storeDataTypes();

        $index  = $this->indexCrdt;
        $search = Search::builder()
            ->withQuery('name_register:Riak*')
            ->withIndex($index)
            ->build();

        $searchResult = $this->client->execute($search);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\SearchResponse', $searchResult);

        $numResults = $searchResult->getNumResults();
        $results    = $searchResult->getSingleResults();

        $this->assertCount(1, $results);
        $this->assertEquals(1, $numResults);
        $this->assertEquals('Riak for dummies', $results[0]['name_register']);
        $this->assertEquals('Fabio B. Silva', $results[0]['author_map.name_register']);
        $this->assertEquals('fabio.bat.silva@gmail.com', $results[0]['author_map.email_register']);
    }

    public function testSimpleKvMultSearch()
    {
        $this->dataKv->storeRiakObjects();

        $index  = $this->indexKv;
        $search = Search::builder()
            ->withQuery('name_s:Riak*')
            ->withIndex($index)
            ->build();

        $searchResult = $this->client->execute($search);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\SearchResponse', $searchResult);

        $numResults = $searchResult->getNumResults();
        $results    = $searchResult->getAllResults();

        $this->assertCount(1, $results);
        $this->assertEquals(1, $numResults);
        $this->assertEquals(['Riak for dummies'], $results[0]['name_s']);
        $this->assertEquals(['it', 'comedy'], $results[0]['category_ss']);
        $this->assertEquals(['Fabio B. Silva'], $results[0]['author_name_s']);
        $this->assertEquals(['fabio.bat.silva@gmail.com'], $results[0]['author_email_s']);
    }
}