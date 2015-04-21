<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Command\MapReduce\Builder\BucketKeyMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\BucketKeyMapReduceOperation;

/**
 * Command used to perform a map reduce operation over a specific set of keys in a bucket.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Core\Query\Func\ErlangFunction;
 *  use Riak\Client\Core\Query\Func\AnonymousJsFunction;
 *  use Riak\Client\Command\MapReduce\BucketKeyMapReduce;
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
 *  $location1  = new RiakLocation($namespace, 'object_key1');
 *  $location2  = new RiakLocation($namespace, 'object_key2');
 *  $command    = BucketKeyMapReduce::builder()
 *      ->withMapPhase($map, null, false)
 *      ->withReducePhase($reduce, null, true)
 *      ->withLocation($location1)
 *      ->withLocation($location2)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\MapReduce\Response\BucketKeyMapReduceResponse
 *  // @var $result array
 *  $response = $client->execute($command);
 *  $result   = $result->getResultForPhase(1);
 *
 *  var_dump($result);
 *  // [10]
 * </code>
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
