<?php

namespace Riak\Client\Core\Operation\MapReduce;

use Riak\Client\Command\MapReduce\Response\MapReduceEntryIterator;
use Riak\Client\Command\MapReduce\Response\BucketMapReduceResponse;

/**
 * A Bucket Map-Reduce Operation on Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketMapReduceOperation extends MapReduceOperation
{
    /**
     * {@inheritdoc}
     */
    protected function createMapReduceResponse(MapReduceEntryIterator $iterator)
    {
        return new BucketMapReduceResponse($iterator);
    }
}
