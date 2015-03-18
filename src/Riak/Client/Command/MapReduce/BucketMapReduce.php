<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\KeyFilters;
use Riak\Client\Command\MapReduce\Builder\BucketMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\BucketMapReduceOperation;

/**
 * Command used to perform a Map Reduce operation over a bucket in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketMapReduce extends MapReduce
{
    /**
     * {@inheritdoc}
     */
    protected function createOperation()
    {
        return new BucketMapReduceOperation(json_encode($this->specification));
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace     $namespace
     * @param \Riak\Client\Command\MapReduce\KeyFilters $filters
     *
     * @return \Riak\Client\Command\MapReduce\Builder\BucketMapReduceBuilder
     */
    public static function builder(RiakNamespace $namespace = null, KeyFilters $filters = null)
    {
        return new BucketMapReduceBuilder($namespace, $filters);
    }
}
