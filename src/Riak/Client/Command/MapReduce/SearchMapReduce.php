<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Command\MapReduce\Builder\SearchMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\SearchMapReduceOperation;

/**
 * Command used to perform a map reduce operation with a search query as input.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchMapReduce extends MapReduce
{
    /**
     * {@inheritdoc}
     */
    protected function createOperation()
    {
        return new SearchMapReduceOperation(json_encode($this->specification));
    }

    /**
     * @param string $index
     * @param string $query
     *
     * @return \Riak\Client\Command\MapReduce\Builder\SearchMapReduceBuilder
     */
    public static function builder($index = null, $query = null)
    {
        return new SearchMapReduceBuilder($index, $query);
    }
}
