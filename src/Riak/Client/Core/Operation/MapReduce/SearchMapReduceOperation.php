<?php

namespace Riak\Client\Core\Operation\MapReduce;

use Riak\Client\Command\MapReduce\Response\MapReduceEntryIterator;
use Riak\Client\Command\MapReduce\Response\SearchMapReduceResponse;

/**
 * A Search Map-Reduce Operation on Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchMapReduceOperation extends MapReduceOperation
{
    /**
     * {@inheritdoc}
     */
    protected function createMapReduceResponse(MapReduceEntryIterator $iterator)
    {
        return new SearchMapReduceResponse($iterator);
    }
}
