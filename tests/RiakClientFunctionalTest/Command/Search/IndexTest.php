<?php

namespace RiakClientFunctionalTest\Command\Search;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Command\Search\FetchIndex;
use Riak\Client\Core\Query\Search\YokozunaIndex;

abstract class IndexTest extends TestCase
{
    public function testStoreAndFetchIndex()
    {
        $indexName = 'schedule_' . uniqid();
        $index     = new YokozunaIndex($indexName, '_yz_default');

        $store = StoreIndex::builder()
            ->withIndex($index)
            ->build();

        $fetch = FetchIndex::builder()
            ->withIndexName('schedule')
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreIndexResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchIndexResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Search\YokozunaIndex', $fetchResponse->getIndex());
        $this->assertEquals('_yz_default', $fetchResponse->getIndex()->getSchema());
        $this->assertEquals('schedule', $fetchResponse->getIndex()->getName());
    }
}