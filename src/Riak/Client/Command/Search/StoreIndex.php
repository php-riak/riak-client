<?php

namespace Riak\Client\Command\Search;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Command\Search\Builder\StoreIndexBuilder;
use Riak\Client\Core\Operation\Search\StoreIndexOperation;

/**
 * Command used to store a search index in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Command\Search\StoreIndex;
 *  use Riak\Client\Core\Query\Search\YokozunaIndex;
 *
 *  $index   = new YokozunaIndex('search_index', '_yz_default');
 *  $command = StoreIndex::builder()
 *      ->withIndex($index)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Search\Response\StoreIndexResponse
 *  $response = $client->execute($command);
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreIndex implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\Search\YokozunaIndex
     */
    private $index;

    /**
     * @param \Riak\Client\Core\Query\Search\YokozunaIndex $index
     */
    public function __construct(YokozunaIndex $index)
    {
        $this->index = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new StoreIndexOperation($this->index);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\Search\YokozunaIndex $schema
     *
     * @return \Riak\Client\Command\Search\Builder\StoreIndexBuilder
     */
    public static function builder(YokozunaIndex $schema = null)
    {
        return new StoreIndexBuilder($schema);
    }
}
