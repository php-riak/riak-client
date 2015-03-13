<?php

namespace Riak\Client\Core\Transport\Http\MapReduce;

use Riak\Client\Core\Transport\Http\MapReduce\HttpMapReduceResponseIterator;
use Riak\Client\Core\Transport\Http\MultipartResponseIterator;
use Riak\Client\Core\Message\MapReduce\MapReduceResponse;
use Riak\Client\Core\Message\MapReduce\MapReduceRequest;
use Riak\Client\Core\Transport\RiakTransportException;
use Riak\Client\Core\Transport\Http\HttpStrategy;
use Riak\Client\Core\Message\Request;
use GuzzleHttp\Stream\Stream;

/**
 * http map-reduce implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpMapReduce extends HttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\MapReduce\MapReduceRequest $mapRedRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(MapReduceRequest $mapRedRequest)
    {
        $request = $this->client->createRequest('POST', '/mapred');
        $query   = $request->getQuery();

        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Accept', 'application/json');
        $query->add('chunked', 'true');

        $request->setBody(Stream::factory($mapRedRequest->request));

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\MapReduce\MapReduceRequest $request
     *
     * @return \Riak\Client\Core\Message\MapReduce\MapReduceResponse
     */
    public function send(Request $request)
    {
        $response     = new MapReduceResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        $multipartIterator  = new MultipartResponseIterator($httpResponse);
        $mapReduceIterator  = new HttpMapReduceResponseIterator($multipartIterator);
        $response->iterator = $mapReduceIterator;

        return $response;
    }
}
