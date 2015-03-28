<?php

namespace Riak\Client\Core\Transport\Http;

use GuzzleHttp\ClientInterface;
use Riak\Client\Core\Transport\Strategy;

/**
 * Base http strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class HttpStrategy implements Strategy
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }
}
