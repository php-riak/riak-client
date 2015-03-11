<?php

namespace Riak\Client\Core\Transport\Http\Kv;

use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Kv\PutRequest;
use Riak\Client\Core\Message\Kv\PutResponse;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpPut extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true,
        201 => true,
        204 => true,
        300 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\Kv\PutRequest $putRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    public function createHttpRequest(PutRequest $putRequest)
    {
        $method      = $putRequest->key ? 'PUT' : 'POST';
        $request     = $this->createRequest($method, $putRequest->type, $putRequest->bucket, $putRequest->key);
        $query       = $request->getQuery();
        $content     = $putRequest->content;
        $contentType = $content->contentType;
        $value       = $content->value;

        foreach ($content->indexes as $name => $indexes) {
            $request->addHeader("X-Riak-Index-$name", $indexes);
        }

        foreach ($content->metas as $name => $meta) {
            $request->addHeader("X-Riak-Meta-$name", $meta);
        }

        $request->setHeader('Accept', ['multipart/mixed', '*/*']);
        $request->setHeader('Content-Type', $contentType);
        $request->setBody(Stream::factory($value));

        if ($putRequest->w !== null) {
            $query->add('w', $putRequest->w);
        }

        if ($putRequest->dw !== null) {
            $query->add('dw', $putRequest->dw);
        }

        if ($putRequest->pw !== null) {
            $query->add('pw', $putRequest->pw);
        }

        if ($putRequest->returnBody !== null) {
            $query->add('returnbody', $putRequest->returnBody ? 'true' : 'false');
        }

        if ($putRequest->vClock !== null) {
            $request->setHeader('X-Riak-Vclock', $putRequest->vClock);
        }

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\PutRequest $request
     *
     * @return \Riak\Client\Core\Message\Kv\PutResponse
     */
    public function send(Request $request)
    {
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();
        $response     = new PutResponse();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        $contentList = $this->getRiakContentList($httpResponse);
        $vClock      = $httpResponse->getHeader('X-Riak-Vclock');
        $key         = null;

        if ($httpResponse->hasHeader('Location')) {
            $location = $httpResponse->getHeader('Location');
            $key      = substr($location, strrpos($location, '/') + 1);
        }

        $response->key         = $key;
        $response->vClock      = $vClock;
        $response->contentList = $contentList;

        return $response;
    }
}
