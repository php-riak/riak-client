<?php

namespace Riak\Client\Core\Adapter\Http\DataType;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\DataType\PutRequest;
use Riak\Client\Core\Message\DataType\PutResponse;
use GuzzleHttp\Stream\Stream;

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
        200 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\DataType\PutRequest $putRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(PutRequest $putRequest)
    {
        $request = $this->createRequest('POST', $putRequest->type, $putRequest->bucket, $putRequest->key);
        $body    = $this->opConverter->toJson($putRequest->op);
        $query   = $request->getQuery();

        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(Stream::factory($body));

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

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\DataType\PutRequest $request
     *
     * @return \Riak\Client\Core\Message\DataType\PutResponse
     */
    public function send(Request $request)
    {
        $response     = new PutResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw new \RuntimeException("Unexpected status code : $code");
        }

        $json  = $httpResponse->json();
        $type  = $json['type'];
        $value = $json['value'];

        $response->type  = $type;
        $response->value = $this->opConverter->fromArray($type, $value);

        return $response;
    }
}
