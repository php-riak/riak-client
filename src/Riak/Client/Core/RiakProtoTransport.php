<?php

namespace Riak\Client\Core;

use Riak\Client\Core\Transport\Proto\ProtoClient;
use Riak\Client\Core\Message\Request;
use InvalidArgumentException;

/**
 * Proto buf transport for riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakProtoTransport implements RiakTransport
{
    private $strategyMap = [
        // kv
        'Riak\Client\Core\Message\Kv\GetRequest'              => 'Riak\Client\Core\Transport\Proto\Kv\ProtoGet',
        'Riak\Client\Core\Message\Kv\PutRequest'              => 'Riak\Client\Core\Transport\Proto\Kv\ProtoPut',
        'Riak\Client\Core\Message\Kv\DeleteRequest'           => 'Riak\Client\Core\Transport\Proto\Kv\ProtoDelete',
        'Riak\Client\Core\Message\Kv\ListKeysRequest'         => 'Riak\Client\Core\Transport\Proto\Kv\ProtoListKeys',
        // crdt
        'Riak\Client\Core\Message\DataType\GetRequest'        => 'Riak\Client\Core\Transport\Proto\DataType\ProtoGet',
        'Riak\Client\Core\Message\DataType\PutRequest'        => 'Riak\Client\Core\Transport\Proto\DataType\ProtoPut',
        // bucket
        'Riak\Client\Core\Message\Bucket\GetRequest'          => 'Riak\Client\Core\Transport\Proto\Bucket\ProtoGet',
        'Riak\Client\Core\Message\Bucket\PutRequest'          => 'Riak\Client\Core\Transport\Proto\Bucket\ProtoPut',
        'Riak\Client\Core\Message\Bucket\ListRequest'         => 'Riak\Client\Core\Transport\Proto\Bucket\ProtoList',
        // index
        'Riak\Client\Core\Message\Index\IndexQueryRequest'    => 'Riak\Client\Core\Transport\Proto\Index\ProtoIndexQuery',
        // search
        'Riak\Client\Core\Message\Search\SearchRequest'       => 'Riak\Client\Core\Transport\Proto\Search\ProtoSearch',
        'Riak\Client\Core\Message\Search\GetSchemaRequest'    => 'Riak\Client\Core\Transport\Proto\Search\ProtoGetSchema',
        'Riak\Client\Core\Message\Search\PutSchemaRequest'    => 'Riak\Client\Core\Transport\Proto\Search\ProtoPutSchema',
        'Riak\Client\Core\Message\Search\GetIndexRequest'     => 'Riak\Client\Core\Transport\Proto\Search\ProtoGetIndex',
        'Riak\Client\Core\Message\Search\PutIndexRequest'     => 'Riak\Client\Core\Transport\Proto\Search\ProtoPutIndex',
        'Riak\Client\Core\Message\Search\DeleteIndexRequest'  => 'Riak\Client\Core\Transport\Proto\Search\ProtoDeleteIndex',
        // map-reduce
        'Riak\Client\Core\Message\MapReduce\MapReduceRequest' => 'Riak\Client\Core\Transport\Proto\MapReduce\ProtoMapReduce',
    ];

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient $client
     */
    public function __construct(ProtoClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return \Riak\Client\Core\Transport\Proto\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param \Riak\Client\Core\Message\Request $request
     *
     * @return \Riak\Client\Core\Transport\Strategy
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
