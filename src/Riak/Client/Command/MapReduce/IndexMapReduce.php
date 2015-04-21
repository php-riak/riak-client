<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\IndexMapReduceOperation;

/**
 * Command used to perform a map reduce operation using a secondary index (2i) as input.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Core\Query\Func\ErlangFunction;
 *  use Riak\Client\Core\Query\Func\AnonymousJsFunction;
 *  use Riak\Client\Command\MapReduce\IndexMapReduce;
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
 *  $namespace  = new RiakNamespace('bucket_type', 'bucket_name');
 *  $command    = IndexMapReduce::builder()
 *      ->withMapPhase($map, null, false)
 *      ->withReducePhase($reduce, null, true)
 *      ->withNamespace($namespace)
 *      ->withMatchValue('even')
 *      ->withIndexBin('tags')
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\MapReduce\Response\IndexMapReduceResponse
 *  // @var $result array
 *  $response = $client->execute($command);
 *  $result   = $result->getResultForPhase(1);
 *
 *  var_dump($result);
 *  // [20]
 * </code>
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
