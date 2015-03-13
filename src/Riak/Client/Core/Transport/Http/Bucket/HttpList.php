<?php

namespace Riak\Client\Core\Transport\Http\Bucket;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Bucket\ListRequest;
use Riak\Client\Core\Message\Bucket\ListResponse;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http list implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpList extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\Bucket\ListRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(ListRequest $getRequest)
    {
        $type    = $getRequest->type;
        $path    = $getRequest->type ? "/types/$type/buckets" : '/buckets';
        $request = $this->client->createRequest('GET', $path);
        $query   = $request->getQuery();

        // stream result dows not work propertly for http :(
        $request->setHeader('Accept', 'application/json');
        $query->add('buckets', 'true');

        if ($getRequest->timeout != null) {
            $query->add('timeout', $getRequest->timeout);
        }

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\ListRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\ListResponse
     */
    public function send(Request $request)
    {
        $response     = new ListResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        $json     = $httpResponse->json();
        $iterator = array_map(function ($item) {
            return new \ArrayIterator([$item]);
        }, $json['buckets']);

        $response->iterator = new \ArrayIterator($iterator);

        return $response;
    }
}
