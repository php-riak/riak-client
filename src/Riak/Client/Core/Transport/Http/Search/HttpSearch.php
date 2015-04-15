<?php

namespace Riak\Client\Core\Transport\Http\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Search\SearchRequest;
use Riak\Client\Core\Message\Search\SearchResponse;

/**
 * http search implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpSearch extends BaseHttpStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\SearchRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(SearchRequest $getRequest)
    {
        $resquest = $this->createQueryRequest('GET', $getRequest->index);
        $query    = $resquest->getQuery();

        $query->add('q', $getRequest->q);
        $query->add('wt', 'json');

        if ($getRequest->presort != null) {
            $query->add('presort', $getRequest->presort);
        }

        if ($getRequest->sort != null) {
            $query->add('sort', $getRequest->sort);
        }

        if ($getRequest->start != null) {
            $query->add('start', $getRequest->start);
        }

        if ($getRequest->rows != null) {
            $query->add('rows', $getRequest->rows);
        }

        if ($getRequest->op != null) {
            $query->add('op', $getRequest->op);
        }

        if ($getRequest->fl != null) {
            $query->add('fl', implode(',', $getRequest->fl));
        }

        if ($getRequest->df != null) {
            $query->add('df', $getRequest->df);
        }

        if ($getRequest->filter != null) {
            $query->add('fq', $getRequest->filter);
        }

        return $resquest;
    }

    /**
     * @param array $doc
     *
     * @return array
     */
    protected function docToArray(array $doc)
    {
        $values = [];

        foreach ($doc as $key => $value) {

            if ( ! is_array($value)) {
                $values[$key][] = $value;

                continue;
            }

            $values[$key] = $value;
        }

        return $values;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response     = new SearchResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $json         = $httpResponse->json();
        $result       = $json['response'];

        $response->docs     = $result['docs'];
        $response->numFound = $result['numFound'];
        $response->maxScore = $result['maxScore'];
        $response->docs     = array_map([$this, 'docToArray'], $result['docs']);

        return $response;
    }
}
