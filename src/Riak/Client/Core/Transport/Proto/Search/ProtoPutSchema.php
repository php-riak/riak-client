<?php

namespace Riak\Client\Core\Transport\Proto\Search;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbYokozunaSchema;
use Riak\Client\ProtoBuf\RpbYokozunaSchemaPutReq;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Search\PutSchemaRequest;
use Riak\Client\Core\Message\Search\PutSchemaResponse;

/**
 * rpb put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoPutSchema extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Search\PutSchemaRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbYokozunaSchemaPutReq
     */
    private function createRpbMessage(PutSchemaRequest $request)
    {
        $rpbSchema = new RpbYokozunaSchema();
        $rpbPutReq = new RpbYokozunaSchemaPutReq();

        $rpbPutReq->setSchema($rpbSchema);
        $rpbSchema->setName($request->name);
        $rpbSchema->setContent($request->content);

        return $rpbPutReq;
    }

    /**
     * @param \Riak\Client\Core\Message\Schema\PutSchemaRequest $request
     *
     * @return \Riak\Client\Core\Message\Schema\PutSchemaResponse
     */
    public function send(Request $request)
    {
        $response   = new PutSchemaResponse();
        $rpbPutReq  = $this->createRpbMessage($request);

        $this->client->send($rpbPutReq, RiakMessageCodes::YOKOZUNA_SCHEMA_PUT_REQ, RiakMessageCodes::PUT_RESP);

        return $response;
    }
}
