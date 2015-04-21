<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\Builder\StoreCounterBuilder;
use Riak\Client\Core\Operation\DataType\StoreCounterOperation;

/**
 * Command used to update or create a counter datatype in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\DataType\StoreCounter;
 *
 *  $namespace = new RiakNamespace('counter_type', 'counter_bucket');
 *  $location  = new RiakLocation($namespace, 'counter_key');
 *  $command   = StoreCounter::builder()
 *      ->withLocation($location)
 *      ->withReturnBody(true)
 *      ->withDelta(10)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\DataType\Response\StoreCounterResponse
 *  // @var $datatype \Riak\Client\Core\Query\Crdt\RiakCounter
 *  $response = $client->execute($command);
 *  $datatype = $response->getDatatype();
 *
 *  echo $datatype->getValue();
 *  // 10
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreCounter extends StoreDataType
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $op        = $this->update->getOp();
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreCounterOperation($converter, $this->location, $op, $this->context, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param array                                $options
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreCounterBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new StoreCounterBuilder($location, $options);
    }
}
