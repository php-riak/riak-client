<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Operation\MapReduce\MapReduceOperation;
use Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder;

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
    public function execute(RiakCluster $cluster)
    {
        $operation = new MapReduceOperation(json_encode($this->specification));
        $response  = $cluster->execute($operation);

        return $response;
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
