<?php

namespace RiakClientFunctionalTest\Command\Search;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Command\Search\FetchIndex;
use Riak\Client\Command\Search\DeleteIndex;
use Riak\Client\Core\Query\Search\YokozunaIndex;

/**
 * @group deprecated
 */
abstract class IndexTest extends TestCase
{
    /**
     * @deprecated
     *
     * not worth functional testing
     * Riak will fail to fetch the index for a couple seconds after store
     */
    public function testStoreAndFetchIndex()
    {
        $indexName = 'schedule_' . uniqid();
        $index     = new YokozunaIndex($indexName, '_yz_default');

        $index->setNVal(3);

        $store = StoreIndex::builder()
            ->withIndex($index)
            ->build();

        $fetch = FetchIndex::builder()
            ->withIndexName($indexName)
            ->build();

        $delete = DeleteIndex::builder()
            ->withIndexName($indexName)
            ->build();

        $storeResponse  = $this->client->execute($store);
        $fetchResponse  = $this->retryCommand($fetch, 10);
        $deleteResponse = $this->client->execute($delete);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreIndexResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchIndexResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Command\Search\Response\DeleteIndexResponse', $deleteResponse);
    }
}