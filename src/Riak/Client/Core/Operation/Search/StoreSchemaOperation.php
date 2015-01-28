<?php

namespace Riak\Client\Core\Operation\Search;

use Riak\Client\Command\Search\Response\StoreSchemaResponse;
use Riak\Client\Core\Message\Search\PutSchemaRequest;
use Riak\Client\Core\Query\Search\YokozunaSchema;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to store an yokozuna schema in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSchemaOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Core\Query\Search\YokozunaSchema
     */
    private $schema;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $schema
     */
    public function __construct(YokozunaSchema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $adapter->send($this->createPutSchemaRequest());

        return new StoreSchemaResponse();
    }

    /**
     * @return \Riak\Client\Core\Message\Search\PutSchemaRequest
     */
    private function createPutSchemaRequest()
    {
        $request = new PutSchemaRequest();

        $request->name    = $this->schema->getName();
        $request->content = $this->schema->getContent();

        return $request;
    }
}
