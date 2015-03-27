<?php

namespace Riak\Client\Core\Operation\MapReduce;

use Riak\Client\Command\MapReduce\Response\MapReduceEntryIterator;
use Riak\Client\Command\MapReduce\Response\BucketKeyMapReduceResponse;

/**
 * A Bucket Key Map-Reduce Operation on Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketKeyMapReduceOperation extends MapReduceOperation
{
    /**
     * {@inheritdoc}
     */
    protected function createMapReduceResponse(MapReduceEntryIterator $iterator)
    {
        return new BucketKeyMapReduceResponse($iterator);
    }
}
