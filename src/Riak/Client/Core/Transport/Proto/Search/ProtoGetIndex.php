<?php

namespace Riak\Client\Core\Transport\Proto\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbYokozunaIndexGetReq;
use Riak\Client\ProtoBuf\RpbYokozunaIndexGetResp;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Search\GetIndexRequest;
use Riak\Client\Core\Message\Search\GetIndexResponse;

/**
 * rpb get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoGetIndex extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\GetIndexRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbGetBucketReq
     */
    private function createRpbMessage(GetIndexRequest $request)
    {
        $rpbGetReq = new RpbYokozunaIndexGetReq();

        $rpbGetReq->setName($request->name);

        return $rpbGetReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $response   = new GetIndexResponse();
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::YOKOZUNA_INDEX_GET_REQ, RiakMessageCodes::YOKOZUNA_INDEX_GET_RESP);

        if ( ! $rpbGetResp instanceof RpbYokozunaIndexGetResp) {
            return $response;
        }

        if ( ! $rpbGetResp->hasIndex()) {
            return $response;
        }

        $rpbIndex = is_array($rpbGetResp->index)
            ? reset($rpbGetResp->index)
            : $rpbGetResp->index;

        $response->nVal   = $rpbIndex->n_val;
        $response->name   = $rpbIndex->name;
        $response->schema = $rpbIndex->schema;

        return $response;
    }
}
