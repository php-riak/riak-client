<?php

namespace Riak\Client\Core\Transport\Http\Search;

use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Search\PutSchemaRequest;
use Riak\Client\Core\Message\Search\PutSchemaResponse;

/**
 * http put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpPutSchema extends BaseHttpStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\PutSchemaRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(PutSchemaRequest $getRequest)
    {
        $request = $this->createSchemaRequest('PUT', $getRequest->name);
        $body    = Stream::factory($getRequest->content);

        $request->setBody($body);
        $request->setHeader('Content-Type', 'application/xml');

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response     = new PutSchemaResponse();
        $httpRequest  = $this->createHttpRequest($request);

        $this->client->send($httpRequest);

        return $response;
    }
}
