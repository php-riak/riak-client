<?php

namespace Riak\Client\Core\Transport\Http\Search;

use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Search\PutIndexRequest;
use Riak\Client\Core\Message\Search\PutIndexResponse;

/**
 * http put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpPutIndex extends BaseHttpStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\PutIndexRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(PutIndexRequest $getRequest)
    {
        $request = $this->createIndexRequest('PUT', $getRequest->name);
        $data    = array_filter([
            'n_val'  => $getRequest->nVal,
            'schema' => $getRequest->schema,
        ]);

        $request->setBody(Stream::factory(json_encode($data)));
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Accept', 'application/json');

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response     = new PutIndexResponse();
        $httpRequest  = $this->createHttpRequest($request);

        $this->client->send($httpRequest);

        return $response;
    }
}
