<?php

namespace Riak\Client\Core\Transport\Proto\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbYokozunaIndex;
use Riak\Client\ProtoBuf\RpbYokozunaIndexPutReq;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Search\PutIndexRequest;
use Riak\Client\Core\Message\Search\PutIndexResponse;

/**
 * rpb put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoPutIndex extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\PutIndexRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbYokozunaIndexPutReq
     */
    private function createRpbMessage(PutIndexRequest $request)
    {
        $rpbIndex  = new RpbYokozunaIndex();
        $rpbPutReq = new RpbYokozunaIndexPutReq();

        $rpbPutReq->setIndex($rpbIndex);
        $rpbIndex->setNVal($request->nVal);
        $rpbIndex->setName($request->name);
        $rpbIndex->setSchema($request->schema);

        return $rpbPutReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Index\PutIndexRequest $request
     *
     * @return \Riak\Client\Core\Message\Index\PutIndexResponse
     */
    public function send(Request $request)
    {
        $response   = new PutIndexResponse();
        $rpbPutReq  = $this->createRpbMessage($request);

        $this->client->send($rpbPutReq, RiakMessageCodes::YOKOZUNA_INDEX_PUT_REQ, RiakMessageCodes::PUT_RESP);

        return $response;
    }
}
