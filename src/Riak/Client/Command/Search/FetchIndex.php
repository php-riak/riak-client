<?php

namespace Riak\Client\Command\Search;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Command\Search\Builder\FetchIndexBuilder;
use Riak\Client\Core\Operation\Search\FetchIndexOperation;

/**
 * Command used to fetch a search schema in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Command\Search\FetchIndex;
 *
 *  $command = FetchIndex::builder()
 *      ->withIndexName('search_index')
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Search\Response\FetchIndexResponse
 *  // @var $response \Riak\Client\Core\Query\Search\YokozunaIndex
 *  $response = $client->execute($command);
 *  $index    = $response->getIndex();
 *
 *  var_dump($index->getSchema());
 *  // "_yz_default"
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchIndex implements RiakCommand
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
        $operation = new FetchIndexOperation($this->indexName);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param string $schemaName
     *
     * @return \Riak\Client\Command\Search\Builder\FetchIndexBuilder
     */
    public static function builder($schemaName = null)
    {
        return new FetchIndexBuilder($schemaName);
    }
}
