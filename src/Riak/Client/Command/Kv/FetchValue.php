<?php

namespace Riak\Client\Command\Kv;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Operation\Kv\FetchOperation;
use Riak\Client\Command\Kv\Builder\FetchValueBuilder;

/**
 * Command used to fetch a value from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchValue implements RiakCommand
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
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param array                               $options
     */
    public function __construct(RiakLocation $location, $options)
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
        $operation = new FetchOperation($config, $this->location, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     *
     * @return \Riak\Client\Command\Kv\Builder\FetchValueBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new FetchValueBuilder($location);
    }
}
