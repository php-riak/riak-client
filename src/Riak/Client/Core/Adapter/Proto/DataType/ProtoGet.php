<?php

namespace Riak\Client\Core\Adapter\Proto\DataType;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\DtFetchReq;
use Riak\Client\ProtoBuf\DtFetchResp;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\DtFetchResp\DataType;
use Riak\Client\Core\Message\DataType\GetRequest;
use Riak\Client\Core\Message\DataType\GetResponse;

/**
 * rpb get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoGet extends BaseProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\DataType\GetRequest $request
     *
     * @return \Riak\Client\ProtoBuf\DtFetchReq
     */
    private function createRpbMessage(GetRequest $request)
    {
        $rpbRequest = new DtFetchReq();

        $rpbRequest->setBucket($request->bucket);
        $rpbRequest->setType($request->type);
        $rpbRequest->setKey($request->key);

        if ($request->r !== null) {
            $rpbRequest->setR($request->r);
        }

        if ($request->pr !== null) {
            $rpbRequest->setPr($request->pr);
        }

        if ($request->basicQuorum !== null) {
            $rpbRequest->setBasicQuorum($request->basicQuorum);
        }

        if ($request->notfoundOk !== null) {
            $rpbRequest->setNotfoundOk($request->notfoundOk);
        }

        return $rpbRequest;
    }

    /**
     * @param \Riak\Client\Core\Message\DataType\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\DataType\GetResponse
     */
    public function send(Request $request)
    {
        $response    = new GetResponse();
        $rpbRequest  = $this->createRpbMessage($request);
        $rpbResponse = $this->client->send($rpbRequest, RiakMessageCodes::DT_FETCH_REQ, RiakMessageCodes::DT_FETCH_RESP);

        if ( ! $rpbResponse instanceof DtFetchResp) {
            return $response;
        }

        $dtType  = $rpbResponse->getType();
        $dtValue = $rpbResponse->getValue()->getOrElse(null);

        if (DataType::COUNTER == $dtType && $dtValue != null) {
            $response->value = $dtValue->counter_value;
            $response->type  = 'counter';
        }

        if (DataType::SET == $dtType && $dtValue != null) {
            $response->value = $dtValue->set_value;
            $response->type  = 'set';
        }

        if (DataType::MAP == $dtType && $dtValue != null) {
            $response->value = $this->opConverter->fromProtoBuf($dtValue->map_value);
            $response->type  = 'map';
        }

        return $response;
    }
}
