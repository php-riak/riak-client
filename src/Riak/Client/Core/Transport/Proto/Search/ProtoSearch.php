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
            foreach ($request->fl as $value) {
                $rpbGetReq->addFl($value);
            }
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
        if ( ! $doc->hasFieldsList()) {
            return [];
        }

        $values = [];

        foreach ($doc->getFieldsList() as $pair) {
            $key   = $pair->getKey()->getContents();
            $value = $pair->getValue()->getContents();

            $values[$key][] = $value;
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

        $response->numFound = $rpbResp->getNumFound();
        $response->maxScore = $rpbResp->getMaxScore();

        if ( ! $rpbResp->hasDocsList()) {
            return $response;
        }

        $response->docs = [];

        foreach ($rpbResp->getDocsList() as $doc) {
            $response->docs[] = $this->docToArray($doc);
        }

        return $response;
    }
}
