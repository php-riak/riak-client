<?php

namespace Riak\Client\Command\Search;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\Search\YokozunaSchema;
use Riak\Client\Command\Search\Builder\StoreSchemaBuilder;
use Riak\Client\Core\Operation\Search\StoreSchemaOperation;

/**
 * Command used to store a search schema in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSchema implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\Search\YokozunaSchema
     */
    private $schema;

    /**
     * @param \Riak\Client\Core\Query\Search\YokozunaSchema $schema
     */
    public function __construct(YokozunaSchema $schema)
    {
        $this->schema = $schema;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new StoreSchemaOperation($this->schema);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\Search\YokozunaSchema $schema
     *
     * @return \Riak\Client\Command\Search\Builder\StoreSchemaBuilder
     */
    public static function builder(YokozunaSchema $schema = null)
    {
        return new StoreSchemaBuilder($schema);
    }
}
