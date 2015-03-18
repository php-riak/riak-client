<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\IndexMapReduceOperation;

/**
 * Command used to perform a map reduce operation using a secondary index (2i) as input. 
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexMapReduce extends MapReduce
{
    /**
     * {@inheritdoc}
     */
    protected function createOperation()
    {
        return new IndexMapReduceOperation(json_encode($this->specification));
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder
     */
    public static function builder(RiakNamespace $namespace = null)
    {
        return new IndexMapReduceBuilder($namespace);
    }
}
