<?php

namespace Riak\Client\Core\Transport\Http\DataType;

use GuzzleHttp\ClientInterface;
use Riak\Client\Core\Transport\Http\HttpStrategy;

/**
 * Base http strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseHttpStrategy extends HttpStrategy
{
    /**
     * @var \Riak\Client\Core\Transport\Http\DataType\CrdtOpConverter
     */
    protected $opConverter;

    /**
     * @param \GuzzleHttp\ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);

        $this->opConverter = new CrdtOpConverter();
    }

    /**
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return string
     */
    protected function buildPath($type, $bucket, $key)
    {
        return sprintf('/types/%s/buckets/%s/datatypes/%s', $type, $bucket, $key);
    }

    /**
     * @param string $method
     * @param string $type
     * @param string $bucket
     * @param string $key
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest($method, $type, $bucket, $key)
    {
        $path    = $this->buildPath($type, $bucket, $key);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }
}
