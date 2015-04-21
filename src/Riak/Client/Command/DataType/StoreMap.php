<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\Builder\StoreMapBuilder;
use Riak\Client\Core\Operation\DataType\StoreMapOperation;

/**
 * Command used to update or create a map datatype in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\DataType\StoreMap;
 *
 *  $namespace = new RiakNamespace('map_type', 'map_bucket');
 *  $location  = new RiakLocation($namespace, 'map_key');
 *  $command   = FetchMap::builder()
 *      ->withLocation($location)
 *      ->withReturnBody(true)
 *      ->updateRegister('name', 'Fabio B. Silva')
 *      ->updateRegister('email', 'fabio.bat.silva@gmail.com')
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\DataType\Response\StoreMapResponse
 *  // @var $datatype \Riak\Client\Core\Query\Crdt\RiakMap
 *  $response = $client->execute($command);
 *  $datatype = $response->getDatatype();
 *
 *  var_dump($datatype->getValue());
 *  // {"name":"Fabio B. Silva","email":"fabio.bat.silva@gmail.com"}
 *
 *  var_dump($datatype->get('name'));
 *  // "Fabio B. Silva"
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreMap extends StoreDataType
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $op        = $this->update->getOp();
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new StoreMapOperation($converter, $this->location, $op, $this->context, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new StoreMapBuilder($location, $options);
    }
}
