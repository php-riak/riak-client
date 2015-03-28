<?php

namespace Riak\Client\Core\Transport\Proto\Kv;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Kv\GetRequest;
use Riak\Client\Core\Message\Kv\GetResponse;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbGetReq;
use Riak\Client\ProtoBuf\RpbGetResp;

/**
 * rpb get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoGet extends BaseProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Kv\GetRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbGetReq
     */
    private function createRpbMessage(GetRequest $request)
    {
        $message = new RpbGetReq();

        $message->setBucket($request->bucket);
        $message->setType($request->type);
        $message->setKey($request->key);

        if ($request->r !== null) {
            $message->setR($this->encodeQuorum($request->r));
        }

        if ($request->pr !== null) {
            $message->setPr($this->encodeQuorum($request->pr));
        }

        if ($request->basicQuorum !== null) {
            $message->setBasicQuorum($request->basicQuorum);
        }

        if ($request->notfoundOk !== null) {
            $message->setNotfoundOk($request->notfoundOk);
        }

        return $message;
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Kv\GetResponse
     */
    public function send(Request $request)
    {
        $response   = new GetResponse();
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::GET_REQ, RiakMessageCodes::GET_RESP);

        if ( ! $rpbGetResp instanceof RpbGetResp) {
            return $response;
        }

        if ( ! $rpbGetResp->hasContent()) {
            return $response;
        }

        $response->vClock      = $rpbGetResp->getVclock()->get();
        $response->contentList = $this->createContentList($rpbGetResp->getContentList());

        return $response;
    }
}
