<?php

namespace RiakClientFunctionalTest\Command\Search;

use Riak\Client\Cap\RiakOption;
use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Search\Search;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Search\FetchIndex;
use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class SearchTest extends TestCase
{
    /**
     * @var string
     */
    protected $indexName = 'test_riak_client_cats';

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    protected function setUp()
    {
        parent::setUp();

        $this->setUpIndex();

        $namespace = new RiakNamespace('default', 'test_riak_client_cats');
        $store     = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::SEARCH_INDEX, $this->indexName)
            ->withProperty(BucketProperties::ALLOW_MULT, false)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);

        $this->namespace = $namespace;
    }

    private function setUpIndex()
    {
        $indexName = $this->indexName;
        $index     = new YokozunaIndex($indexName, '_yz_default');

        $store = StoreIndex::builder()
            ->withIndex($index)
            ->build();

        $fetch = FetchIndex::builder()
            ->withIndexName($indexName)
            ->build();

        try {
            $this->client->execute($fetch);
        } catch (\Exception $exc) {
            $this->client->execute($store);
            $this->retryCommand($fetch, 10);
        }
    }

    private function storeThunderCat($key, RiakObject $object)
    {
        $location = new RiakLocation($this->namespace, $key);
        $command  = StoreValue::builder($location, $object)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $this->client->execute($command);
    }

    protected function createThunderCat()
    {
        $this->storeThunderCat("lion", new RiakObject(json_encode([
            'name_s' => 'Lion-o',
            'leader' => true,
            'age'    => 30,
        ]), 'application/json'));

        $this->storeThunderCat("cheetara", new RiakObject(json_encode([
            'name_s' => 'Cheetara',
            'leader' => false,
            'age'    => 30,
        ]), 'application/json'));

        $this->storeThunderCat("snarf", new RiakObject(json_encode([
            'name_s' => 'Snarf',
            'leader' => false,
            'age'    => 43,
        ]), 'application/json'));

        $this->storeThunderCat("panthro", new RiakObject(json_encode([
            'name_s' => 'Panthro',
            'leader' => false,
            'age'    => 36,
        ]), 'application/json'));
    }

    public function testSearch()
    {
        $this->createThunderCat();

        $index  = $this->indexName;
        $search = Search::builder()
            ->withQuery('name_s:*')
            ->withIndex($index)
            ->build();

        $searchResult = $this->client->execute($search);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\SearchResponse', $searchResult);
        //$this->assertEquals(4, $searchResult->getNumResults());
    }
}