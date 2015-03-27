<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Command\MapReduce\Builder\BucketKeyMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\BucketKeyMapReduceOperation;

/**
 * Command used to perform a map reduce operation over a specific set of keys in a bucket.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketKeyMapReduce extends MapReduce
{
    /**
     * {@inheritdoc}
     */
    protected function createOperation()
    {
        return new BucketKeyMapReduceOperation(json_encode($this->specification));
    }

    /**
     * @param \Riak\Client\Command\MapReduce\Input\BucketKey\Input[] $inputs
     *
     * @return \Riak\Client\Command\MapReduce\Builder\BucketKeyMapReduceBuilder
     */
    public static function builder(array $inputs = [])
    {
        return new BucketKeyMapReduceBuilder($inputs);
    }
}
