<?php

namespace Riak\Client\Core\Transport\Proto\Kv;

use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;
use Riak\Client\Core\Message\Kv\ListKeysResponse;
use Riak\Client\Core\Message\Kv\ListKeysRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbListKeysReq;
use Riak\Client\Core\Message\Request;

/**
 * rpb list keys implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoListKeys extends BaseProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Kv\ListKeysRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbGetReq
     */
    private function createRpbMessage(ListKeysRequest $request)
    {
        $message = new RpbListKeysReq();

        $message->setTimeout($request->timeout);
        $message->setBucket($request->bucket);
        $message->setType($request->type);

        return $message;
    }

    /**
     * @param \Riak\Client\Core\Message\Kv\ListKeysRequest $request
     *
     * @return \Riak\Client\Core\Message\Kv\ListKeysResponse
     */
    public function send(Request $request)
    {
        $response  = new ListKeysResponse();
        $rpbGetReq = $this->createRpbMessage($request);
        $socket    = $this->client->emit($rpbGetReq, RiakMessageCodes::LIST_KEYS_REQ);
        $iterator  = new ProtoStreamIterator($this->client, $socket, RiakMessageCodes::LIST_KEYS_RESP);

        $response->iterator = new ProtoListKeysResponseIterator($iterator);

        return $response;
    }
}
