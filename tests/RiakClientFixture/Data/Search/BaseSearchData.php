<?php

namespace RiakClientFixture\Data\Search;

use Riak\Client\RiakClient;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Search\FetchIndex;
use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Command\Bucket\StoreBucketProperties;
use RiakClientFunctionalTest\TestHelper;

abstract class BaseSearchData
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

    protected function setUpBucket()
    {
        $store = StoreBucketProperties::builder()
            ->withSearchIndex($this->indexName)
            ->withNamespace($this->namespace)
            ->withAllowMulti(false)
            ->build();

        $this->client->execute($store);
    }

    protected function getSchemaType()
    {
        return '_yz_default';
    }

    private function setUpIndex()
    {
        $indexName = $this->indexName;
        $schema    = $this->getSchemaType();
        $index     = new YokozunaIndex($indexName, $schema);

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

    protected function storeObject($key, RiakObject $object)
    {
        if ($this->isStored($key)) {
            return;
        }

        if ($this->isIndexed($key)) {
            return;
        }

        $location = new RiakLocation($this->namespace, $key);
        $command  = StoreValue::builder($location, $object)
            ->withPw(1)
            ->withW(2)
            ->build();

        $this->client->execute($command);
        $this->insureIsIndexed($key);
    }

    protected function isStored($key)
    {
        $location = new RiakLocation($this->namespace, $key);
        $command  = FetchValue::builder($location)
            ->withNotFoundOk(true)
            ->build();

        $response = $this->client->execute($command);

        return $response->getNotFound() == false;
    }

    protected function isIndexed($key)
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

    protected function insureIsIndexed($key)
    {
        $retry = 10;

        do {
            $isIndexed = $this->isIndexed($key);
            $retry     = $retry -1;

            if ($isIndexed) {
                return;
            }

            sleep(1);

        } while ($retry < 10);

        throw new \RuntimeException('Unable to index object : ' . $key);
    }
}