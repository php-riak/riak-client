<?php

namespace Riak\Client\Core\Transport\Http\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Search\GetSchemaRequest;
use Riak\Client\Core\Message\Search\GetSchemaResponse;

/**
 * http get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpGetSchema extends BaseHttpStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\GetSchemaRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetSchemaRequest $getRequest)
    {
        return $this->createSchemaRequest('GET', $getRequest->name);
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response     = new GetSchemaResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);

        $response->name    = $request->name;
        $response->content = (string) $httpResponse->getBody();

        return $response;
    }
}
