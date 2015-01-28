<?php

namespace Riak\Client\Command\Search;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Command\Search\Builder\FetchSchemaBuilder;
use Riak\Client\Core\Operation\Search\FetchSchemaOperation;

/**
 * Command used to fetch a search schema in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSchema implements RiakCommand
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
    public function execute(RiakCluster $cluster)
    {
        $operation = new FetchSchemaOperation($this->schemaName);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param string $schemaName
     *
     * @return \Riak\Client\Command\Search\Builder\FetchSchemaBuilder
     */
    public static function builder($schemaName = null)
    {
        return new FetchSchemaBuilder($schemaName);
    }
}
