<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\Builder\FetchCounterBuilder;
use Riak\Client\Core\Operation\DataType\FetchCounterOperation;

/**
 * Command used to fetch a counter datatype from Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\DataType\FetchCounter;
 *
 *  $namespace = new RiakNamespace('counter_type', 'counter_bucket');
 *  $location  = new RiakLocation($namespace, 'counter_key');
 *  $command   = FetchCounter::builder()
 *      ->withLocation($location)
 *      ->withIncludeContext(true)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\DataType\Response\FetchCounterResponse
 *  // @var $datatype \Riak\Client\Core\Query\Crdt\RiakCounter
 *  $response = $client->execute($command);
 *  $datatype = $response->getDatatype();
 *
 *  echo $datatype->getValue();
 *  // 3
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchCounter extends FetchDataType
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new FetchCounterOperation($converter, $this->location, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param array                                $options
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchCounterBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new FetchCounterBuilder($location, $options);
    }
}
