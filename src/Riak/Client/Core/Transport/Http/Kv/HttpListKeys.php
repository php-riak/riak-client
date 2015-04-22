<?php

namespace Riak\Client\Core\Transport\Http\Kv;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Kv\ListKeysRequest;
use Riak\Client\Core\Message\Kv\ListKeysResponse;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http list keys implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpListKeys extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true,
        300 => true
    ];

    /**
     * @param string $type
     * @param string $bucket
     *
     * @return string
     */
    protected function buildKeysPath($type, $bucket)
    {
        if ($type === null) {
            return sprintf('/buckets/%s/keys', $bucket);
        }

        return sprintf('/types/%s/buckets/%s/keys', $type, $bucket);
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\ListKeysRequest $listRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(ListKeysRequest $listRequest)
    {
        $path    = $this->buildKeysPath($listRequest->type, $listRequest->bucket);
        $request = $this->client->createRequest('GET', $path);
        $query   = $request->getQuery();

        // stream result does not work propertly for http ? :(
        $request->setHeader('Accept', 'application/json');
        $query->add('keys', 'true');

        if ($listRequest->timeout != null) {
            $query->add('timeout', $listRequest->timeout);
        }

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\ListKeysRequest $request
     *
     * @return \Riak\Client\Core\Message\Kv\ListKeysResponse
     */
    public function send(Request $request)
    {
        $response     = new ListKeysResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        $json     = $httpResponse->json();
        $iterator = array_map(function ($item) {
            return new \ArrayIterator([$item]);
        }, $json['keys']);

        $response->iterator = new \ArrayIterator($iterator);

        return $response;
    }
}
