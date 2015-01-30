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
    protected function buildPath($schema)
    {
        return sprintf('/search/schema/%s', $schema);
    }

    /**
     * @param string $method
     * @param string $schema
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest($method, $schema)
    {
        $path    = $this->buildPath($schema);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }
}
