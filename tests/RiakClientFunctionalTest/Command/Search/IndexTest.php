<?php

namespace RiakClientFunctionalTest\Command\Search;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Command\Search\FetchIndex;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Core\Transport\RiakTransportException;

abstract class IndexTest extends TestCase
{
    /**
     * @param \Riak\Client\Command\Search\FetchIndex $fetch
     * @param integer $retryCount
     *
     * @return \Riak\Client\Command\Search\Response\FetchIndexResponse
     */
    public function retryFetch(FetchIndex $fetch, $retryCount)
    {
        try {
            return $this->client->execute($fetch);
        } catch (RiakTransportException $exc) {

            if ($retryCount <= 0) {
                throw $exc;
            }

            sleep(1);

            return $this->retryFetch($fetch, -- $retryCount);
        }
    }

    public function testStoreAndFetchIndex()
    {
        // not worth functional testing
        $this->markTestSkipped('Riak will fail to fetch the index for a couple seconds after store');

        $indexName = 'schedule_' . uniqid();
        $index     = new YokozunaIndex($indexName, '_yz_default');

        $index->setNVal(3);

        $store = StoreIndex::builder()
            ->withIndex($index)
            ->build();

        $fetch = FetchIndex::builder()
            ->withIndexName($indexName)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->retryFetch($fetch, 10);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreIndexResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchIndexResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Search\YokozunaIndex', $fetchResponse->getIndex());
        $this->assertEquals('_yz_default', $fetchResponse->getIndex()->getSchema());
        $this->assertEquals($indexName, $fetchResponse->getIndex()->getName());
        $this->assertEquals(3, $fetchResponse->getIndex()->getNVal());
    }
}