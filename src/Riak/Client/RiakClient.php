<?php

namespace Riak\Client;

use Riak\Client\Core\RiakCluster;

/**
 * The client used to perform operations on Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakClient
{
    /**
     * @var \Riak\Client\Core\RiakCluster
     */
    private $cluster;

    /**
     * @var \Riak\Client\RiakConfig
     */
    private $config;

    /**
     * @param \Riak\Client\RiakConfig       $config
     * @param \Riak\Client\Core\RiakCluster $cluster
     */
    public function __construct(RiakConfig $config, RiakCluster $cluster)
    {
        $this->config  = $config;
        $this->cluster = $cluster;
    }

    /**
     * @return \Riak\Client\Core\RiakCluster
     */
    public function getCluster()
    {
        return $this->cluster;
    }

    /**
     * @return \Riak\Client\RiakConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Execute a RiakCommand.
     *
     * Calling this method causes the client to execute the provided RiakCommand.
     *
     * @param \Riak\Client\RiakCommand $command
     *
     * @return \Riak\Client\RiakResponse
     */
    public function execute(RiakCommand $command)
    {
        return $command->execute($this->cluster);
    }
}
