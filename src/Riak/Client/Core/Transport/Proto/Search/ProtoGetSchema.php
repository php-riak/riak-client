<?php

namespace Riak\Client\Core\Transport\Proto\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbYokozunaSchemaGetReq;
use Riak\Client\ProtoBuf\RpbYokozunaSchemaGetResp;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Search\GetSchemaRequest;
use Riak\Client\Core\Message\Search\GetSchemaResponse;

/**
 * rpb get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoGetSchema extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\GetSchemaRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbYokozunaSchemaGetReq
     */
    private function createRpbMessage(GetSchemaRequest $request)
    {
        $rpbGetReq = new RpbYokozunaSchemaGetReq();

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
        $response   = new GetSchemaResponse();
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::YOKOZUNA_SCHEMA_GET_REQ, RiakMessageCodes::YOKOZUNA_SCHEMA_GET_RESP);

        if ( ! $rpbGetResp instanceof RpbYokozunaSchemaGetResp) {
            return $response;
        }

        if ( ! $rpbGetResp->hasSchema()) {
            return $response;
        }

        $response->name    = $rpbGetResp->schema->name;
        $response->content = $rpbGetResp->schema->content;

        return $response;
    }
}
