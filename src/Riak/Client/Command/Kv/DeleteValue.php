<?php

namespace Riak\Client\Command\Kv;

use Riak\Client\Cap\VClock;
use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Operation\Kv\DeleteOperation;
use Riak\Client\Command\Kv\Builder\DeleteValueBuilder;

/**
 * Command used to delete a value from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteValue implements RiakCommand
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
     * @var \Riak\Client\Cap\VClock
     */
    private $vClock;

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location, $options)
    {
        $this->location = $location;
        $this->options  = $options;
    }

    /**
     * @param \Riak\Client\Cap\VClock $vClock
     *
     * @return \Riak\Client\Command\DeleteValue
     */
    public function withVClock(VClock $vClock)
    {
        $this->vClock = $vClock;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $operation = new DeleteOperation($config, $this->location, $this->options, $this->vClock);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     *
     * @return \Riak\Client\Command\Kv\Builder\DeleteValueBuilder
     */
    public static function builder(RiakLocation $location = null)
    {
        return new DeleteValueBuilder($location);
    }
}
