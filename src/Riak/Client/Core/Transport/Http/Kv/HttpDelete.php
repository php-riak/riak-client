<?php

namespace Riak\Client\Core\Transport\Http\Kv;

use Riak\Client\Core\Message\Request;
use GuzzleHttp\Exception\RequestException;
use Riak\Client\Core\Message\Kv\DeleteRequest;
use Riak\Client\Core\Message\Kv\DeleteResponse;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http delete implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpDelete extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true,
        204 => true,
        404 => true,
    ];

    /**
     * @param \Riak\Client\Core\Message\Kv\DeleteRequest $deleteRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    public function createHttpRequest(DeleteRequest $deleteRequest)
    {
        $request  = $this->createRequest('DELETE', $deleteRequest->type, $deleteRequest->bucket, $deleteRequest->key);
        $query    = $request->getQuery();

        $request->setHeader('Accept', ['multipart/mixed', '*/*']);

        if ($deleteRequest->vClock) {
            $request->setHeader('X-Riak-Vclock', $deleteRequest->vClock);
        }

        if ($deleteRequest->r !== null) {
            $query->add('r', $deleteRequest->r);
        }

        if ($deleteRequest->pr !== null) {
            $query->add('pr', $deleteRequest->pr);
        }

        if ($deleteRequest->rw !== null) {
            $query->add('rw', $deleteRequest->rw);
        }

        if ($deleteRequest->w !== null) {
            $query->add('w', $deleteRequest->w);
        }

        if ($deleteRequest->dw !== null) {
            $query->add('dw', $deleteRequest->dw);
        }

        if ($deleteRequest->pw !== null) {
            $query->add('pw', $deleteRequest->pw);
        }

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\DeleteRequest $request
     *
     * @return \Riak\Client\Core\Message\Kv\DeleteResponse
     */
    public function send(Request $request)
    {
        $httpRequest  = $this->createHttpRequest($request);
        $response     = new DeleteResponse();
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

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
