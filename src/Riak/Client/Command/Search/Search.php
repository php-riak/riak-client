<?php

namespace Riak\Client\Command\Search;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakSearchQuery;
use Riak\Client\Command\Search\Builder\SearchBuilder;
use Riak\Client\Core\Operation\Search\SearchOperation;

/**
 * Command used to perform a serach in Riak Yokozuna.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Search implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakSearchQuery
     */
    private $query;

    /**
     * @param \Riak\Client\Core\Query\RiakSearchQuery $query
     */
    public function __construct(RiakSearchQuery $query)
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new SearchOperation($this->query);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakSearchQuery
     */
    public function getSearchQuery()
    {
        return $this->query;
    }

    /**
     * @return \Riak\Client\Command\Search\Builder\SearchBuilder
     */
    public static function builder()
    {
        return new SearchBuilder();
    }
}
