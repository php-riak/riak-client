<?php

namespace Riak\Client\Core\Transport\Proto\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbYokozunaIndexDeleteReq;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Search\DeleteIndexRequest;
use Riak\Client\Core\Message\Search\DeleteIndexResponse;

/**
 * rpb delete implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoDeleteIndex extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\DeleteIndexRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbDeleteBucketReq
     */
    private function createRpbMessage(DeleteIndexRequest $request)
    {
        $rpbDeleteReq = new RpbYokozunaIndexDeleteReq();

        $rpbDeleteReq->setName($request->name);

        return $rpbDeleteReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\DeleteRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\DeleteResponse
     */
    public function send(Request $request)
    {
        $response      = new DeleteIndexResponse();
        $rpbDeleteReq  = $this->createRpbMessage($request);

        $this->client->send($rpbDeleteReq, RiakMessageCodes::YOKOZUNA_INDEX_DELETE_REQ, RiakMessageCodes::DEL_RESP);

        return $response;
    }
}
