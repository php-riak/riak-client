<?php

namespace Riak\Client\Command\Search;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Command\Search\Builder\FetchSchemaBuilder;
use Riak\Client\Core\Operation\Search\FetchSchemaOperation;

/**
 * Command used to fetch a search schema in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Command\Search\FetchSchema;
 *
 *  $command = FetchSchema::builder()
 *      ->withSchemaName('search_schema')
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Search\Response\StoreSchemaResponse
 *  // @var $response \Riak\Client\Core\Query\Search\YokozunaSchema
 *  $response = $client->execute($command);
 *  $schema   = $response->getSchema();
 *
 *  var_dump($schema->getContent());
 *  // "<xml>...</xml>"
 * </code>
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
