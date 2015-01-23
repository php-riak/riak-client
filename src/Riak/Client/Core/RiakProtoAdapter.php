<?php

namespace Riak\Client\Core;

use Riak\Client\Core\Adapter\Proto\ProtoClient;
use Riak\Client\Core\Message\Request;
use InvalidArgumentException;

/**
 * Proto buf adapter for riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakProtoAdapter implements RiakAdapter
{
    private $strategyMap = [
        // kv
        'Riak\Client\Core\Message\Kv\GetRequest'       => 'Riak\Client\Core\Adapter\Proto\Kv\ProtoGet',
        'Riak\Client\Core\Message\Kv\PutRequest'       => 'Riak\Client\Core\Adapter\Proto\Kv\ProtoPut',
        'Riak\Client\Core\Message\Kv\DeleteRequest'    => 'Riak\Client\Core\Adapter\Proto\Kv\ProtoDelete',
        // crdt
        'Riak\Client\Core\Message\DataType\GetRequest' => 'Riak\Client\Core\Adapter\Proto\DataType\ProtoGet',
        'Riak\Client\Core\Message\DataType\PutRequest' => 'Riak\Client\Core\Adapter\Proto\DataType\ProtoPut',
        // bucket
        'Riak\Client\Core\Message\Bucket\GetRequest'   => 'Riak\Client\Core\Adapter\Proto\Bucket\ProtoGet',
        'Riak\Client\Core\Message\Bucket\PutRequest'   => 'Riak\Client\Core\Adapter\Proto\Bucket\ProtoPut',
    ];

    /**
     * @var \Riak\Client\Core\Adapter\Proto\ProtoClient
     */
    private $client;

    /**
     * @param \Riak\Client\Core\Adapter\Proto\ProtoClient $client
     */
    public function __construct(ProtoClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return \Riak\Client\Core\Adapter\Proto\Client
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
