<?php

namespace Riak\Client\Core\Operation\Search;

use Riak\Client\Command\Search\Response\FetchSchemaResponse;
use Riak\Client\Core\Message\Search\GetSchemaRequest;
use Riak\Client\Core\Query\Search\YokozunaSchema;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to fetch an yokozuna schema in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSchemaOperation implements RiakOperation
{
    /**
     * @var string
     */
    private $schemaName;

    /**
     * @param string $schemaName
     */
    public function __construct($schemaName)
    {
        $this->schemaName = $schemaName;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $response = $adapter->send($this->createGetSchemaRequest());
        $schema   = ($response && $response->name)
            ? new YokozunaSchema($response->name, $response->content)
            : null;

        return new FetchSchemaResponse($schema);
    }

    /**
     * @return \Riak\Client\Core\Message\Search\GetSchemaRequest
     */
    private function createGetSchemaRequest()
    {
        $request = new GetSchemaRequest();

        $request->name = $this->schemaName;

        return $request;
    }
}
