<?php

namespace Riak\Client\Core\Transport\Proto\Index;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RpbIndexReq;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbIndexReq\IndexQueryType;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Message\Index\IndexQueryResponse;
use Riak\Client\Core\Operation\Index\IndexEntryIterator;
use Riak\Client\Core\Transport\Proto\Index\ProtoIndexQueryIterator;

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

        return $rpbGetReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     *
     * @return \Riak\Client\Core\Message\Index\IndexQueryResponse
     */
    public function send(Request $request)
    {
        $client   = clone $this->client;
        $response = new IndexQueryResponse();
        $rpbReq   = $this->createRpbMessage($request);
        $iterator = new ProtoIndexQueryIterator($request, $client, RiakMessageCodes::INDEX_RESP);

        $client->emit($rpbReq, RiakMessageCodes::INDEX_REQ);

        $response->iterator = $iterator;

        return $response;
    }
}
