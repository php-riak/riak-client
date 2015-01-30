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
     * @param string $schema
     *
     * @return string
     */
    protected function buildSchemaPath($schema)
    {
        return sprintf('/search/schema/%s', $schema);
    }

    /**
     * @param string $index
     *
     * @return string
     */
    protected function buildIndexPath($index)
    {
        return sprintf('/search/index/%s', $index);
    }

    /**
     * @param string $method
     * @param string $schema
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createSchemaRequest($method, $schema)
    {
        $path    = $this->buildSchemaPath($schema);
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
        $path    = $this->buildIndexPath($schema);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }
}
