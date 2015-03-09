<?php

namespace Riak\Client\Core\Transport\Proto\MapReduce;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RpbMapRedReq;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\MapReduce\MapReduceRequest;
use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;
use Riak\Client\Core\Message\MapReduce\MapReduceResponse;
use Riak\Client\Core\Transport\Proto\MapReduce\ProtoMapReduceResponseIterator;

/**
 * rpb Map-Reduce implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoMapReduce extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\MapReduce\MapReduceRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbMapRedReq
     */
    private function createRpbMessage(MapReduceRequest $request)
    {
        $rpbMapRedReq = new RpbMapRedReq();

        $rpbMapRedReq->setContentType('application/json');
        $rpbMapRedReq->setRequest($request->request);

        return $rpbMapRedReq;
    }

    /**
     * @param \Riak\Client\Core\Message\MapReduce\MapReduceRequest $request
     *
     * @return \Riak\Client\Core\Message\Index\IndexQueryResponse
     */
    public function send(Request $request)
    {
        $response = new MapReduceResponse();
        $rpbReq   = $this->createRpbMessage($request);
        $socket   = $this->client->emit($rpbReq, RiakMessageCodes::MAPRED_REQ);
        $iterator = new ProtoStreamIterator($this->client, $socket, RiakMessageCodes::MAPRED_RESP);

        $response->iterator = new ProtoMapReduceResponseIterator($iterator);

        return $response;
    }
}
