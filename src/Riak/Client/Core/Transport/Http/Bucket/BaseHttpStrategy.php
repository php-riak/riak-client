<?php

namespace Riak\Client\Core\Transport\Http\Bucket;

use Riak\Client\Core\Transport\Http\HttpStrategy;

/**
 * Base http strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class BaseHttpStrategy extends HttpStrategy
{
    /**
     * @param string $type
     * @param string $bucket
     *
     * @return string
     */
    protected function buildPath($type, $bucket)
    {
        if ($type === null) {
            return sprintf('/buckets/%s/props', $bucket);
        }

        return sprintf('/types/%s/buckets/%s/props', $type, $bucket);
    }

    /**
     * @param string $method
     * @param string $type
     * @param string $bucket
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest($method, $type, $bucket)
    {
        $path    = $this->buildPath($type, $bucket);
        $httpReq = $this->client->createRequest($method, $path);

        return $httpReq;
    }
}
