<?php

namespace Riak\Client\Command\Search;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Command\Search\Builder\DeleteIndexBuilder;
use Riak\Client\Core\Operation\Search\DeleteIndexOperation;

/**
 * Command used to delete a search index in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteIndex implements RiakCommand
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
    public function execute(RiakCluster $cluster)
    {
        $operation = new DeleteIndexOperation($this->indexName);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param string $schemaName
     *
     * @return \Riak\Client\Command\Search\Builder\DeleteIndexBuilder
     */
    public static function builder($schemaName = null)
    {
        return new DeleteIndexBuilder($schemaName);
    }
}
