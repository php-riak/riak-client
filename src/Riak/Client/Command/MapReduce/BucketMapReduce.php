<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\KeyFilters;
use Riak\Client\Command\MapReduce\Builder\BucketMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\BucketMapReduceOperation;

/**
 * Command used to perform a Map Reduce operation over a bucket in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\MapReduce\KeyFilters;
 *  use Riak\Client\Core\Query\Func\ErlangFunction;
 *  use Riak\Client\Core\Query\Func\AnonymousJsFunction;
 *  use Riak\Client\Command\MapReduce\BucketMapReduce;
 *
 *  $reduce = new ErlangFunction('riak_kv_mapreduce', 'reduce_sum');
 *  $map    = new AnonymousJsFunction('function(value) {
 *    for (i = 0; i < value.values.length; i++) {
 *        if (value.values[i].metadata["X-Riak-Deleted"]) {
 *            continue;
 *        }
 *
 *        return [JSON.parse(value.values[i].data)];
 *    }
 *
 *    return [];
 *  }');
 *
 *  $filter     = KeyFilters::filter()->between('1', '10', false);
 *  $namespace  = new RiakNamespace('bucket_type', 'bucket_name');
 *  $command    = BucketMapReduce::builder()
 *      ->withMapPhase($map, null, false)
 *      ->withReducePhase($reduce, null, true)
 *      ->withNamespace($namespace)
 *      ->withKeyFilter($filter)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\MapReduce\Response\BucketMapReduceResponse
 *  // @var $result array
 *  $response = $client->execute($command);
 *  $result   = $result->getResultForPhase(1);
 *
 *  var_dump($result);
 *  // [9]
 * </code>
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
