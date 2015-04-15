<?php

namespace Riak\Client\Core\Transport\Http\DataType;

use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\DataType\PutRequest;
use Riak\Client\Core\Message\DataType\PutResponse;
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
        204 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\DataType\PutRequest $putRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(PutRequest $putRequest)
    {
        $request = $this->createRequest('POST', $putRequest->type, $putRequest->bucket, $putRequest->key);
        $data    = $this->opConverter->convert($putRequest->op);
        $query   = $request->getQuery();

        if ($putRequest->context !== null && is_array($data)) {
            $data['context'] = $putRequest->context;
        }

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

        if ($putRequest->includeContext !== null) {
            $query->add('include_context', $putRequest->includeContext ? 'true' : 'false');
        }

        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(Stream::factory(json_encode($data)));

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
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        if ($code === 204) {
            return $response;
        }

        $json    = $httpResponse->json();
        $context = isset($json['context']) ? $json['context'] : null;
        $value   = $json['value'];
        $type    = $json['type'];

        $response->type    = $type;
        $response->context = $context;
        $response->value   = $this->opConverter->fromArray($type, $value);

        return $response;
    }
}
