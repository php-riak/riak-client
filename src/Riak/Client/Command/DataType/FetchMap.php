<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\Builder\FetchMapBuilder;
use Riak\Client\Core\Operation\DataType\FetchMapOperation;

/**
 * Command used to fetch a counter datatype from Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\DataType\FetchMap;
 *
 *  $namespace = new RiakNamespace('map_type', 'map_bucket');
 *  $location  = new RiakLocation($namespace, 'map_key');
 *  $command   = FetchMap::builder()
 *      ->withLocation($location)
 *      ->withIncludeContext(true)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\DataType\Response\FetchMapResponse
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
class FetchMap implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $converter = $config->getCrdtResponseConverter();
        $operation = new FetchMapOperation($converter, $this->location, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchMapBuilder
     */
    public static function builder(RiakLocation $location = null, array $options = [])
    {
        return new FetchMapBuilder($location, $options);
    }
}
