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
    protected $indexName;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    protected function setUp()
    {
        parent::setUp();

        $hash      = hash('crc32', __CLASS__ );
        $bucket    = sprintf('test_riak_client_%s_cats', $hash);
        $index     = sprintf('test_riak_client_%s_famous', $hash);
        $namespace = new RiakNamespace('default', $bucket);

        $this->indexName = $index;
        $this->namespace = $namespace;

        $this->setUpIndex();
        $this->setUpBucket();
    }

    private function setUpBucket()
    {
        $store = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::SEARCH_INDEX, $this->indexName)
            ->withProperty(BucketProperties::ALLOW_MULT, false)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($this->namespace)
            ->build();

        $this->client->execute($store);
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
            $this->retryCommand($fetch, 20);
        }
    }

    private function storeThunderCat($key, RiakObject $object)
    {
        if ($this->isThunderCatIndexed($key)) {
            return;
        }

        $location = new RiakLocation($this->namespace, $key);
        $command  = StoreValue::builder($location, $object)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $this->client->execute($command);
        $this->insureThunderCatIsIndexed($key);
    }

    private function storeThunderCats()
    {
        $this->storeThunderCat("lion", new RiakObject(json_encode([
            'name_s'   => 'Lion-o',
            'leader_b' => true,
            'age_i'    => 30,
        ]), 'application/json'));

        $this->storeThunderCat("cheetara", new RiakObject(json_encode([
            'name_s'   => 'Cheetara',
            'leader_b' => false,
            'age_i'    => 30,
        ]), 'application/json'));

        $this->storeThunderCat("snarf", new RiakObject(json_encode([
            'name_s'   => 'Snarf',
            'leader_b' => false,
            'age_i'    => 43,
        ]), 'application/json'));

        $this->storeThunderCat("panthro", new RiakObject(json_encode([
            'name_s'   => 'Panthro',
            'leader_b' => false,
            'age_i'    => 36,
        ]), 'application/json'));
    }

    private function isThunderCatIndexed($key)
    {
        $baseUrl = $this->createInternalSolarBucketUri($this->indexName, 'select');
        $client  = $this->createGuzzleClient($baseUrl);
        $request = $client->createRequest('GET');
        $query   = $request->getQuery();

        $query->add('q', "_yz_rk:$key");
        $query->add('wt', 'json');

        $response = $client->send($request);
        $json     = $response->json();

        return ($json['response']['numFound'] > 0);
    }

    private function insureThunderCatIsIndexed($key)
    {
        $retry = 10;

        do {
            $isIndexed = $this->isThunderCatIndexed($key);
            $retry     = $retry -1;

            if ($isIndexed) {
                return;
            }

            sleep(1);

        } while ($retry < 10);

        $this->fail('Unable to index Thunder Cat : ' . $key);
    }

    public function testSimpleSearch()
    {
        $this->storeThunderCats();

        $index       = $this->indexName;
        $searchSnarf = Search::builder()
            ->withQuery('name_s:Snarf')
            ->withIndex($index)
            ->build();

        $searchSnarfResult = $this->client->execute($searchSnarf);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\SearchResponse', $searchSnarfResult);

        $numResults = $searchSnarfResult->getNumResults();
        $results    = $searchSnarfResult->getResults();

        $this->assertCount(1, $results);
        $this->assertEquals(1, $numResults);
        $this->assertArrayHasKey('name_s', $results[0]);
        $this->assertEquals('Snarf', $results[0]['name_s']);
    }

    public function testSearchReturnFields()
    {
        $this->storeThunderCats();

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
        $results    = $searchResult->getResults();

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