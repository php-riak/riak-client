<?php

namespace Riak\Client\Core\Transport\Http\Index;

use Riak\Client\Core\Transport\Http\Index\HttpIndexQueryResponseIterator;
use Riak\Client\Core\Transport\Http\MultipartResponseIterator;
use Riak\Client\Core\Transport\RiakTransportException;
use Riak\Client\Core\Message\Index\IndexQueryResponse;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Http\HttpStrategy;
use Riak\Client\Core\Message\Request;

/**
 * http 2l query implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpIndexQuery extends HttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true
    ];

    /**
     * @param string $type
     * @param string $bucket
     * @param string $index
     * @param array  $args
     *
     * @return string
     */
    protected function buildPath($type, $bucket, $index, array $args)
    {
        $path = empty($args)
            ? ''
            : implode('/', $args);

        if ($type === null) {
            return sprintf('/buckets/%s/index/%s/%s', $bucket, $index, $path);
        }

        return sprintf('/types/%s/buckets/%s/index/%s/%s', $type, $bucket, $index, $path);
    }

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $getRequest
     *
     * @return array
     */
    protected function createRequestArgs(IndexQueryRequest $getRequest)
    {
        $values = ($getRequest->qtype == 'range')
            ? [$getRequest->rangeMin, $getRequest->rangeMax]
            : [$getRequest->key];

        return array_filter($values);
    }

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    protected function createRequest(IndexQueryRequest $getRequest)
    {
        $args    = $this->createRequestArgs($getRequest);
        $path    = $this->buildPath($getRequest->type, $getRequest->bucket, $getRequest->index, $args);
        $httpReq = $this->client->createRequest('GET', $path);

        return $httpReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(IndexQueryRequest $getRequest)
    {
        $request = $this->createRequest($getRequest);
        $query   = $request->getQuery();

        $request->setHeader('Accept', 'application/json');
        $query->add('stream', 'true');

        if ($getRequest->returnTerms !== null) {
            $query->add('return_terms', $getRequest->returnTerms ? 'true' : 'false');
        }

        if ($getRequest->termRegex !== null) {
            $query->add('term_regex', $getRequest->termRegex);
        }

        if ($getRequest->maxResults !== null) {
            $query->add('max_results', $getRequest->maxResults);
        }

        if ($getRequest->continuation !== null) {
            $query->add('continuation', $getRequest->continuation);
        }

        if ($getRequest->paginationSort !== null) {
            $query->add('pagination_sort', $getRequest->paginationSort);
        }

        if ($getRequest->timeout !== null) {
            $query->add('timeout', $getRequest->timeout);
        }

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response     = new IndexQueryResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        $multipartIterator  = new MultipartResponseIterator($httpResponse);
        $responseIterator   = new HttpIndexQueryResponseIterator($request, $multipartIterator);
        $response->iterator = $responseIterator;

        return $response;
    }
}
