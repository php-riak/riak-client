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
        $schema   = null;
        $response = $adapter->send($this->createGetIndexRequest());

        if ($response && $response->name) {
            $schema = new YokozunaIndex($response->name, $response->schema);

            $schema->setNVal($response->nVal);
        }

        return new FetchIndexResponse($schema);
    }

    /**
     * @return \Riak\Client\Core\Message\Search\GetIndexRequest
     */
    private function createGetIndexRequest()
    {
        $request = new GetIndexRequest();

        $request->name = $this->indexName;

        return $request;
    }
}
