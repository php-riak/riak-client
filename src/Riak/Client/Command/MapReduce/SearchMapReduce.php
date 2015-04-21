<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\Command\MapReduce\Builder\SearchMapReduceBuilder;
use Riak\Client\Core\Operation\MapReduce\SearchMapReduceOperation;

/**
 * Command used to perform a map reduce operation with a search query as input.
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
 *  $reduce = new ErlangFunction('riak_kv_mapreduce', 'reduce_sort');
 *  $map    = new AnonymousJsFunction('function(value) {
 *    for (i = 0; i < value.values.length; i++) {
 *        if (value.values[i].metadata["X-Riak-Deleted"]) {
 *            continue;
 *        }
 *
 *        return [JSON.parse(value.values[i].data).name_s];
 *    }
 *
 *    return [];
 *  }');
 *
 *  $namespace  = new RiakNamespace('bucket_type', 'bucket_name');
 *  $command    = IndexMapReduce::builder()
 *      ->withMapPhase($map, null, false)
 *      ->withReducePhase($reduce, null, true)
 *      ->withIndex('thunder_cats')
 *      ->withQuery('age_i:30')
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\MapReduce\Response\SearchMapReduceResponse
 *  // @var $result array
 *  $response = $client->execute($command);
 *  $result   = $result->getResultForPhase(1);
 *
 *  var_dump($result);
 *  // ['Cheetara', 'Lion-o']
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchMapReduce extends MapReduce
{
    /**
     * {@inheritdoc}
     */
    protected function createOperation()
    {
        return new SearchMapReduceOperation(json_encode($this->specification));
    }

    /**
     * @param string $index
     * @param string $query
     *
     * @return \Riak\Client\Command\MapReduce\Builder\SearchMapReduceBuilder
     */
    public static function builder($index = null, $query = null)
    {
        return new SearchMapReduceBuilder($index, $query);
    }
}
