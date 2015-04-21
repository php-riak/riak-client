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
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Command\Search\Search;
 *
 *  $command = Search::builder()
 *      ->withReturnFields(['name_s', 'age_i'])
 *      ->withIndex('thunder_cats')
 *      ->withQuery('name_s:Snarf')
 *      ->withNumRows(10)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Search\Response\SearchResponse
 *  // @var $results array
 *  $response = $client->execute($command);
 *  $results  = $response->getAllResults();
 *
 *  var_dump($results[0]['name_s']);
 *  // ['Snarf']
 * </code>
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
