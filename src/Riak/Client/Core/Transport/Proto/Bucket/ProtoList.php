<?php

namespace Riak\Client\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbListBucketsReq;
use Riak\Client\Core\Message\Bucket\ListRequest;
use Riak\Client\Core\Message\Bucket\ListResponse;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;

/**
 * rpb list implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoList extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Bucket\ListRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbListBucketsReq
     */
    private function createRpbMessage(ListRequest $request)
    {
        $rpbGetReq = new RpbListBucketsReq();

        if ($request->timeout != null) {
            $rpbGetReq->setTimeout($request->timeout);
        }

        if ($request->type != null) {
            $rpbGetReq->setType($request->type);
        }

        $rpbGetReq->setStream(true);

        return $rpbGetReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\ListRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\ListResponse
     */
    public function send(Request $request)
    {
        $response = new ListResponse();
        $rpbReq   = $this->createRpbMessage($request);
        $socket   = $this->client->emit($rpbReq, RiakMessageCodes::LIST_BUCKETS_REQ);
        $iterator = new ProtoStreamIterator($this->client, $socket, RiakMessageCodes::LIST_BUCKETS_RESP);

        $response->iterator = new ProtoListResponseIterator($iterator);

        return $response;
    }
}
