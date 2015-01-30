<?php

namespace Riak\Client\Core\Transport\Http\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Search\GetIndexRequest;
use Riak\Client\Core\Message\Search\GetIndexResponse;

/**
 * http get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpGetIndex extends BaseHttpStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\GetIndexRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetIndexRequest $getRequest)
    {
        $resquest = $this->createIndexRequest('GET', $getRequest->name);

        $resquest->setHeader('Accept', 'application/json');

        return $resquest;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response     = new GetIndexResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $json         = $httpResponse->json();

        $response->name   = $request->name;
        $response->nVal   = $json['n_val'];
        $response->schema = $json['schema'];

        return $response;
    }
}
