<?php

namespace Riak\Client\Core\Transport\Http\DataType;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\DataType\GetRequest;
use Riak\Client\Core\Message\DataType\GetResponse;
use GuzzleHttp\Exception\RequestException;

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
        200 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\DataType\GetRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetRequest $getRequest)
    {
        $request = $this->createRequest('GET', $getRequest->type, $getRequest->bucket, $getRequest->key);
        $query   = $request->getQuery();

        $request->setHeader('Accept', 'application/json');

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
     * @param \Riak\Client\Core\Message\DataType\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\DataType\GetResponse
     */
    public function send(Request $request)
    {
        $response    = new GetResponse();
        $httpRequest = $this->createHttpRequest($request);

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
