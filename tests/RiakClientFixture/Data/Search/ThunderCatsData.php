<?php

namespace RiakClientFixture\Data\Search;

use Riak\Client\RiakOption;
use Riak\Client\RiakClient;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Search\FetchIndex;
use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Command\Bucket\StoreBucketProperties;
use RiakClientFunctionalTest\TestHelper;

class ThunderCatsData
{
    /**
     * @var \Riak\Client\RiakClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    /**
     * @param RiakNamespace $namespace
     * @param string        $indexName
     */
    public function __construct(RiakClient $client, RiakNamespace $namespace, $indexName)
    {
        $this->client    = $client;
        $this->indexName = $indexName;
        $this->namespace = $namespace;
    }

    public function setUp()
    {
        $this->setUpIndex();
        $this->setUpBucket();
    }

    private function setUpBucket()
    {
        $store = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::SEARCH_INDEX, $this->indexName)
            ->withProperty(BucketProperties::ALLOW_MULT, false)
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
            TestHelper::retryCommand($this->client, $fetch, 20);
        }
    }

    public function storeThunderCats()
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

    private function isThunderCatIndexed($key)
    {
        $baseUrl = TestHelper::createInternalSolarBucketUri($this->indexName, 'select');
        $client  = TestHelper::createGuzzleClient($baseUrl);
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

        throw new \RuntimeException('Unable to index Thunder Cat : ' . $key);
    }
}