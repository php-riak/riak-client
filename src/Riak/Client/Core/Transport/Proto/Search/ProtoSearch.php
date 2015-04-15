<?php

namespace Riak\Client\Core\Transport\Proto\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RpbSearchDoc;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbSearchQueryReq;
use Riak\Client\ProtoBuf\RpbSearchQueryResp;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Search\SearchRequest;
use Riak\Client\Core\Message\Search\SearchResponse;

/**
 * rpb search implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoSearch extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\SearchRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbSearchQueryReq
     */
    private function createRpbMessage(SearchRequest $request)
    {
        $rpbGetReq = new RpbSearchQueryReq();

        $rpbGetReq->setQ($request->q);
        $rpbGetReq->setIndex($request->index);

        if ($request->presort != null) {
            $rpbGetReq->setPresort($request->presort);
        }

        if ($request->sort != null) {
            $rpbGetReq->setSort($request->sort);
        }

        if ($request->start != null) {
            $rpbGetReq->setStart($request->start);
        }

        if ($request->rows != null) {
            $rpbGetReq->setRows($request->rows);
        }

        if ($request->op != null) {
            $rpbGetReq->setOp($request->op);
        }

        if ($request->fl != null) {
            $rpbGetReq->setFl($request->fl);
        }

        if ($request->df != null) {
            $rpbGetReq->setDf($request->df);
        }

        if ($request->filter != null) {
            $rpbGetReq->setFilter($request->filter);
        }

        return $rpbGetReq;
    }

    /**
     * @param \Riak\Client\ProtoBuf\RpbSearchDoc $doc
     *
     * @return array
     */
    protected function docToArray(RpbSearchDoc $doc)
    {
        if ( ! $doc->hasFields()) {
            return [];
        }

        $values = [];

        foreach ($doc->fields as $pair) {
            $values[$pair->key][] = $pair->value;
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
        $response = new SearchResponse();
        $rpbReq   = $this->createRpbMessage($request);
        $rpbResp  = $this->client->send($rpbReq, RiakMessageCodes::SEARCH_QUERY_REQ, RiakMessageCodes::SEARCH_QUERY_RESP);

        if ( ! $rpbResp instanceof RpbSearchQueryResp) {
            return $response;
        }

        $response->numFound = $rpbResp->num_found;
        $response->maxScore = $rpbResp->max_score;

        if ( ! $rpbResp->hasDocs()) {
            return $response;
        }

        $response->docs = array_map([$this, 'docToArray'], $rpbResp->docs);

        return $response;
    }
}
