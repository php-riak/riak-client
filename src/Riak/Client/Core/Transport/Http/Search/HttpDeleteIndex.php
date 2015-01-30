<?php

namespace Riak\Client\Core\Transport\Http\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Search\DeleteIndexRequest;
use Riak\Client\Core\Message\Search\DeleteIndexResponse;

/**
 * http delete implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpDeleteIndex extends BaseHttpStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\DeleteIndexRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(DeleteIndexRequest $getRequest)
    {
        return $this->createIndexRequest('DELETE', $getRequest->name);
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response     = new DeleteIndexResponse();
        $httpRequest  = $this->createHttpRequest($request);

        $this->client->send($httpRequest);

        return $response;
    }
}
