<?php

namespace Riak\Client\Core\Transport\Proto;

use Riak\Client\Core\Transport\Strategy;
use Riak\Client\Core\Transport\Proto\ProtoClient;

/**
 * Base rpb strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class ProtoStrategy implements Strategy
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    protected $client;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient $client
     */
    public function __construct(ProtoClient $client)
    {
        $this->client = $client;
    }
}
