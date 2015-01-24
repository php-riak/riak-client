<?php

namespace Riak\Client\Core\Transport\Http\Kv;

use Riak\Client\Core\Message\Request;
use GuzzleHttp\Exception\RequestException;
use Riak\Client\Core\Message\Kv\GetRequest;
use Riak\Client\Core\Message\Kv\GetResponse;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpGet extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true,
        300 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\Kv\GetRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetRequest $getRequest)
    {
        $request = $this->createRequest('GET', $getRequest->type, $getRequest->bucket, $getRequest->key);
        $query   = $request->getQuery();

        $request->setHeader('Accept', ['multipart/mixed', '*/*']);

        if ($getRequest->r !== null) {
            $query->add('r', $getRequest->r);
        }

        if ($getRequest->pr !== null) {
            $query->add('pr', $getRequest->pr);
        }

        if ($getRequest->basicQuorum !== null) {
            $query->add('basic_quorum', $getRequest->basicQuorum ? 'true' : 'false');
        }

        if ($getRequest->notfoundOk !== null) {
            $query->add('notfound_ok', $getRequest->notfoundOk ? 'true' : 'false');
        }

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Kv\GetResponse
     */
    public function send(Request $request)
    {
        $httpRequest = $this->createHttpRequest($request);
        $response    = new GetResponse();

        try {
            $httpResponse = $this->client->send($httpRequest);
            $code         = $httpResponse->getStatusCode();
        } catch (RequestException $e) {
            if ($e->getCode() == 404 && $request->notfoundOk) {
                return $response;
            }

            throw $e;
        }

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        $contentList = $this->getRiakContentList($httpResponse);
        $vClock      = $httpResponse->getHeader('X-Riak-Vclock');

        $response->vClock = $vClock;
        $response->contentList = $contentList;

        return $response;
    }
}
