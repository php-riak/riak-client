<?php

namespace Riak\Client\Core;

use InvalidArgumentException;
use GuzzleHttp\ClientInterface;
use Riak\Client\Core\Message\Request;

/**
 * Http adapter for riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakHttpAdpter implements RiakAdapter
{
    private $strategyMap = [
        // kv
        'Riak\Client\Core\Message\Kv\GetRequest'       => 'Riak\Client\Core\Adapter\Http\Kv\HttpGet',
        'Riak\Client\Core\Message\Kv\PutRequest'       => 'Riak\Client\Core\Adapter\Http\Kv\HttpPut',
        'Riak\Client\Core\Message\Kv\DeleteRequest'    => 'Riak\Client\Core\Adapter\Http\Kv\HttpDelete',
        // crdt
        'Riak\Client\Core\Message\DataType\GetRequest' => 'Riak\Client\Core\Adapter\Http\DataType\HttpGet',
        'Riak\Client\Core\Message\DataType\PutRequest' => 'Riak\Client\Core\Adapter\Http\DataType\HttpPut',
        // bucket
        'Riak\Client\Core\Message\Bucket\GetRequest'   => 'Riak\Client\Core\Adapter\Http\Bucket\HttpGet',
        'Riak\Client\Core\Message\Bucket\PutRequest'   => 'Riak\Client\Core\Adapter\Http\Bucket\HttpPut',
    ];

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Riak\Client\Core\Message\Request $request
     *
     * @return \Riak\Client\Core\Adapter\Strategy
     */
    private function createAdapterStrategyFor(Request $request)
    {
        $requestClass  = get_class($request);
        $strategyClass = isset($this->strategyMap[$requestClass])
            ? $this->strategyMap[$requestClass]
            : null;

        if ($strategyClass !== null) {
            return new $strategyClass($this->client);
        }

        throw new InvalidArgumentException(sprintf("Unknown message : %s", get_class($request)));
    }

    /**
     * {@inheritdoc}
     */
    public function send(Request $request)
    {
        return $this->createAdapterStrategyFor($request)->send($request);
    }
}
