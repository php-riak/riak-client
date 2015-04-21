<?php

namespace Riak\Client\Command\Kv;

use Riak\Client\Core\Query\VClock;
use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Operation\Kv\DeleteOperation;
use Riak\Client\Command\Kv\Builder\DeleteValueBuilder;

/**
 * Command used to delete a value from Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Command\Kv\DeleteValue;
 *  use Riak\Client\Core\Query\RiakLocation;
 *  use Riak\Client\Core\Query\RiakNamespace;
 *
 *  $namespace = new RiakNamespace('bucket_type', 'bucket_name');
 *  $location  = new RiakLocation($namespace, 'object_key');
 *  $command   = FetchValue::builder()
 *      ->withLocation($location)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Kv\Response\DeleteValueResponse
 *  $response = $client->execute($command);
 * </code>
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
     * @var \Riak\Client\Core\Query\VClock
     */
    private $vClock;

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param \Riak\Client\Core\Query\VClock       $vClock
     * @param array                                $options
     */
    public function __construct(RiakLocation $location, VClock $vClock = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->vClock   = $vClock;
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
