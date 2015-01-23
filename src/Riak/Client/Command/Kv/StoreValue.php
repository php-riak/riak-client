<?php

namespace Riak\Client\Command\Kv;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Operation\Kv\StoreOperation;
use Riak\Client\Command\Kv\Builder\StoreValueBuilder;

/**
 * Command used to store a value in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreValue implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param \Riak\Client\Core\Query\RiakLocation     $location
     * @param \Riak\Client\Core\Query\RiakObject|mixed $value
     * @param array                                    $options
     */
    public function __construct(RiakLocation $location, $value = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->value    = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $config    = $cluster->getRiakConfig();
        $operation = new StoreOperation($config, $this->location, $this->value, $this->options);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation     $location
     * @param \Riak\Client\Core\Query\RiakObject|mixed $value
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public static function builder(RiakLocation $location = null, $value = null)
    {
        return new StoreValueBuilder($location, $value);
    }
}
