<?php

namespace Riak\Client\Core\Transport\Proto\Index;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RpbIndexReq;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbIndexReq\IndexQueryType;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Message\Index\IndexQueryResponse;
use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;
use Riak\Client\Core\Transport\Proto\Index\ProtoIndexQueryResponseIterator;

/**
 * rpb index query implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoIndexQuery extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbIndexReq
     */
    private function createRpbMessage(IndexQueryRequest $request)
    {
        $rpbGetReq = new RpbIndexReq();

        $rpbGetReq->setBucket($request->bucket);
        $rpbGetReq->setIndex($request->index);
        $rpbGetReq->setType($request->type);
        $rpbGetReq->setStream(true);

        if ($request->qtype === 'eq') {
            $rpbGetReq->setKey($request->key);
            $rpbGetReq->setQtype(IndexQueryType::eq);
        }

        if ($request->qtype === 'range') {
            $rpbGetReq->setRangeMin($request->rangeMin);
            $rpbGetReq->setRangeMax($request->rangeMax);
            $rpbGetReq->setQtype(IndexQueryType::range);
        }

        if ($request->returnTerms !== null) {
            $rpbGetReq->setReturnTerms($request->returnTerms);
        }

        if ($request->termRegex !== null) {
            $rpbGetReq->setTermRegex($request->termRegex);
        }

        if ($request->maxResults !== null) {
            $rpbGetReq->setMaxResults($request->maxResults);
        }

        if ($request->continuation !== null) {
            $rpbGetReq->setContinuation($request->continuation);
        }

        if ($request->paginationSort !== null) {
            $rpbGetReq->setPaginationSort($request->paginationSort);
        }

        if ($request->timeout !== null) {
            $rpbGetReq->setTimeout($request->timeout);
        }

        return $rpbGetReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     *
     * @return \Riak\Client\Core\Message\Index\IndexQueryResponse
     */
    public function send(Request $request)
    {
        $response = new IndexQueryResponse();
        $rpbReq   = $this->createRpbMessage($request);
        $socket   = $this->client->emit($rpbReq, RiakMessageCodes::INDEX_REQ);
        $iterator = new ProtoStreamIterator($this->client, $socket, RiakMessageCodes::INDEX_RESP);

        $response->iterator = new ProtoIndexQueryResponseIterator($request, $iterator);

        return $response;
    }
}
