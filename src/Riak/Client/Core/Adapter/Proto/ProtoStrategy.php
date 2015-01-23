<?php

namespace Riak\Client\Core\Adapter\Proto;

use Riak\Client\Core\Adapter\Strategy;
use Riak\Client\Core\Adapter\Proto\ProtoClient;

/**
 * Base rpb strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class ProtoStrategy implements Strategy
{
    /**
     * @var \Riak\Client\Core\Adapter\Proto\ProtoClient
     */
    protected $client;

    /**
     * @param \Riak\Client\Core\Adapter\Proto\ProtoClient $client
     */
    public function __construct(ProtoClient $client)
    {
        $this->client = $client;
    }
}
