<?php

namespace Riak\Client;

use Riak\Client\Core\RiakCluster;

/**
 * The base class for all Riak Commands.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface RiakCommand
{
    /**
     * @param \Riak\Client\Core\RiakCluster $cluster
     *
     * @return \Riak\Client\RiakResponse
     */
    public function execute(RiakCluster $cluster);
}
