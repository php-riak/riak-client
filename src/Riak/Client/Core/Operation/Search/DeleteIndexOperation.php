<?php

namespace Riak\Client\Core\Operation\Search;

use Riak\Client\Command\Search\Response\DeleteIndexResponse;
use Riak\Client\Core\Message\Search\DeleteIndexRequest;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to fetch an yokozuna schema in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteIndexOperation implements RiakOperation
{
    /**
     * @var string
     */
    private $indexName;

    /**
     * @param string $indexName
     */
    public function __construct($indexName)
    {
        $this->indexName = $indexName;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $adapter->send($this->createDeleteIndexRequest());

        return new DeleteIndexResponse();
    }

    /**
     * @return \Riak\Client\Core\Message\Search\DeleteIndexRequest
     */
    private function createDeleteIndexRequest()
    {
        $request = new DeleteIndexRequest();

        $request->name = $this->indexName;

        return $request;
    }
}
