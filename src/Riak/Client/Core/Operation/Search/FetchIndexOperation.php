<?php

namespace Riak\Client\Core\Operation\Search;

use Riak\Client\Command\Search\Response\FetchIndexResponse;
use Riak\Client\Core\Message\Search\GetIndexRequest;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to fetch an yokozuna schema in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchIndexOperation implements RiakOperation
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
        $response = $adapter->send($this->createGetIndexRequest());
        $schema   = ($response && $response->name)
            ? new YokozunaIndex($response->name, $response->schema)
            : null;

        return new FetchIndexResponse($schema);
    }

    /**
     * @return \Riak\Client\Core\Message\Search\GetIndexRequest
     */
    private function createGetIndexRequest()
    {
        $request = new GetIndexRequest();

        $request->name = $this->schemaName;

        return $request;
    }
}
