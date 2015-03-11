<?php

namespace Riak\Client\Command\Index;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Operation\Index\IndexQueryOperation;
use Riak\Client\Command\Index\Builder\BinIndexQueryBuilder;

/**
 * Performs a 2i query where the 2i keys are ints.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BinIndexQuery extends IndexQuery
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new IndexQueryOperation($this->query);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param string                                $indexName
     * @param mixed                                 $start
     * @param mixed                                 $end
     *
     * @return \Riak\Client\Command\Index\Builder\BinIndexQueryBuilder
     */
    public static function builder(RiakNamespace $namespace = null, $indexName = null, $start = null, $end = null)
    {
        return new BinIndexQueryBuilder($namespace, $indexName, $start, $end);
    }
}
