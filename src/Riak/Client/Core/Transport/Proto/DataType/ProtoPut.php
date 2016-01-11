<?php

namespace Riak\Client\Core\Transport\Proto\DataType;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\DtUpdateReq;
use Riak\Client\ProtoBuf\DtUpdateResp;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Message\DataType\PutRequest;
use Riak\Client\Core\Message\DataType\PutResponse;

/**
 * rpb put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoPut extends BaseProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\DataType\PutRequest $request
     *
     * @return \Riak\Client\ProtoBuf\DtUpdateReq
     */
    private function createRpbMessage(PutRequest $request)
    {
        $rpbPutReq = new DtUpdateReq();
        $crdtOp    = $this->opConverter->toProtoBuf($request->op);

        $rpbPutReq->setBucket($request->bucket);
        $rpbPutReq->setType($request->type);
        $rpbPutReq->setKey($request->key);

        if ($request->w !== null) {
            $rpbPutReq->setW($this->encodeQuorum($request->w));
        }

        if ($request->dw !== null) {
            $rpbPutReq->setDw($this->encodeQuorum($request->dw));
        }

        if ($request->pw !== null) {
            $rpbPutReq->setPw($this->encodeQuorum($request->pw));
        }

        if ($request->returnBody !== null) {
            $rpbPutReq->setReturnBody($request->returnBody);
        }

        if ($request->includeContext !== null) {
            $rpbPutReq->setIncludeContext($request->includeContext);
        }

        if ($request->context !== null) {
            $rpbPutReq->setContext($request->context);
        }

        $rpbPutReq->setOp($crdtOp);

        return $rpbPutReq;
    }

    /**
     * @param \Riak\Client\Core\Message\DataType\PutRequest $request
     *
     * @return \Riak\Client\Core\Message\DataType\PutResponse
     */
    public function send(Request $request)
    {
        $response   = new PutResponse();
        $rpbPutReq  = $this->createRpbMessage($request);
        $rpbPutResp = $this->client->send($rpbPutReq, RiakMessageCodes::DT_UPDATE_REQ, RiakMessageCodes::DT_UPDATE_RESP);

        if ( ! $rpbPutResp instanceof DtUpdateResp) {
            return $response;
        }

        if ($rpbPutResp->hasContext()) {
            $response->context = $rpbPutResp->getContext()->getContents();
        }

        if ($rpbPutResp->hasCounterValue()) {
            $response->value = $rpbPutResp->getCounterValue();
            $response->type  = 'counter';

            return $response;
        }

        if ($rpbPutResp->hasSetValueList()) {
            $response->type = 'set';

            foreach ($rpbPutResp->getSetValueList() as $value) {
                $response->value[] = $value->getContents();
            }

            return $response;
        }

        if ($rpbPutResp->hasMapValueList()) {
            $response->value = $this->opConverter->fromProtoBuf($rpbPutResp->getMapValueList());
            $response->type  = 'map';
        }

        return $response;
    }
}
