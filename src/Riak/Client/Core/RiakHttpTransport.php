<?php

namespace Riak\Client\Core;

use InvalidArgumentException;
use GuzzleHttp\ClientInterface;
use Riak\Client\Core\Message\Request;
use GuzzleHttp\Exception\RequestException;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http transport for riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakHttpTransport implements RiakTransport
{
    private $strategyMap = [
        // kv
        'Riak\Client\Core\Message\Kv\GetRequest'              => 'Riak\Client\Core\Transport\Http\Kv\HttpGet',
        'Riak\Client\Core\Message\Kv\PutRequest'              => 'Riak\Client\Core\Transport\Http\Kv\HttpPut',
        'Riak\Client\Core\Message\Kv\DeleteRequest'           => 'Riak\Client\Core\Transport\Http\Kv\HttpDelete',
        'Riak\Client\Core\Message\Kv\ListKeysRequest'         => 'Riak\Client\Core\Transport\Http\Kv\HttpListKeys',
        // crdt
        'Riak\Client\Core\Message\DataType\GetRequest'        => 'Riak\Client\Core\Transport\Http\DataType\HttpGet',
        'Riak\Client\Core\Message\DataType\PutRequest'        => 'Riak\Client\Core\Transport\Http\DataType\HttpPut',
        // bucket
        'Riak\Client\Core\Message\Bucket\GetRequest'          => 'Riak\Client\Core\Transport\Http\Bucket\HttpGet',
        'Riak\Client\Core\Message\Bucket\PutRequest'          => 'Riak\Client\Core\Transport\Http\Bucket\HttpPut',
        'Riak\Client\Core\Message\Bucket\ListRequest'         => 'Riak\Client\Core\Transport\Http\Bucket\HttpList',
        // index
        'Riak\Client\Core\Message\Index\IndexQueryRequest'    => 'Riak\Client\Core\Transport\Http\Index\HttpIndexQuery',
        // search
        'Riak\Client\Core\Message\Search\SearchRequest'       => 'Riak\Client\Core\Transport\Http\Search\HttpSearch',
        'Riak\Client\Core\Message\Search\GetSchemaRequest'    => 'Riak\Client\Core\Transport\Http\Search\HttpGetSchema',
        'Riak\Client\Core\Message\Search\PutSchemaRequest'    => 'Riak\Client\Core\Transport\Http\Search\HttpPutSchema',
        'Riak\Client\Core\Message\Search\GetIndexRequest'     => 'Riak\Client\Core\Transport\Http\Search\HttpGetIndex',
        'Riak\Client\Core\Message\Search\PutIndexRequest'     => 'Riak\Client\Core\Transport\Http\Search\HttpPutIndex',
        'Riak\Client\Core\Message\Search\DeleteIndexRequest'  => 'Riak\Client\Core\Transport\Http\Search\HttpDeleteIndex',
        // map-reduce
        'Riak\Client\Core\Message\MapReduce\MapReduceRequest' => 'Riak\Client\Core\Transport\Http\MapReduce\HttpMapReduce',
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
        try {
            return $this->createAdapterStrategyFor($request)->send($request);
        } catch (RequestException $exc) {
            throw RiakTransportException::httpRequestException($exc);
        }
    }
}
