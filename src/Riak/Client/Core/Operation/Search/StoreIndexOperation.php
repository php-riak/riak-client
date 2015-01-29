<?php

namespace Riak\Client\Core\Operation\Search;

use Riak\Client\Command\Search\Response\StoreIndexResponse;
use Riak\Client\Core\Message\Search\PutIndexRequest;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to store an yokozuna schema in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreIndexOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Core\Query\Search\YokozunaIndex
     */
    private $schema;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $schema
     */
    public function __construct(YokozunaIndex $schema)
    {
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $adapter->send($this->createPutIndexRequest());

        return new StoreIndexResponse();
    }

    /**
     * @return \Riak\Client\Core\Message\Search\PutIndexRequest
     */
    private function createPutIndexRequest()
    {
        $request = new PutIndexRequest();

        $request->nVal   = $this->schema->getNVal();
        $request->name   = $this->schema->getName();
        $request->schema = $this->schema->getSchema();

        return $request;
    }
}
