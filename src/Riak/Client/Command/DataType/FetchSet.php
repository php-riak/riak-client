<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\Builder\FetchSetBuilder;
use Riak\Client\Core\Operation\DataType\FetchSetOperation;

/**
 * Command used to fetch a set datatype from Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\DataType\FetchSet;
 *
 *  $namespace = new RiakNamespace('set_type', 'set_bucket');
 *  $location  = new RiakLocation($namespace, 'set_key');
 *  $command   = FetchSet::builder()
 *      ->withLocation($location)
 *      ->withIncludeContext(true)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\DataType\Response\FetchSetResponse
 *  // @var $datatype \Riak\Client\Core\Query\Crdt\RiakSet
 *  $response = $client->execute($command);
 *  $datatype = $response->getDatatype();
 *
 *  var_dump($datatype->getValue());
 *  // ["Ottawa","Toronto"]
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSet extends FetchDataType
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new FetchSetOperation($converter, $this->location, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchSetBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new FetchSetBuilder($location, $options);
    }
}
