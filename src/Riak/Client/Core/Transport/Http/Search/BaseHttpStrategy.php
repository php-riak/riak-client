<?php

namespace Riak\Client\Core\Transport\Http\Search;

use Riak\Client\Core\Transport\Http\HttpStrategy;

/**
 * Base http strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseHttpStrategy extends HttpStrategy
{
    /**
     * @param string $resource
     * @param string $path
     *
     * @return string
     */
    protected function buildPath($resource, $path)
    {
        return sprintf('/search/%s/%s', $resource, $path);
    }

    /**
     * @param string $method
     * @param string $schema
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createSchemaRequest($method, $schema)
    {
        $path    = $this->buildPath('schema', $schema);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }

    /**
     * @param string $method
     * @param string $schema
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createIndexRequest($method, $schema)
    {
        $path    = $this->buildPath('index', $schema);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }

    /**
     * @param string $method
     * @param string $schema
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createQueryRequest($method, $schema)
    {
        $path    = $this->buildPath('query', $schema);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }
}
