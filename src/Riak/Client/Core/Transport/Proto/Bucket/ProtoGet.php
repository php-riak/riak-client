<?php

namespace Riak\Client\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RpbGetBucketReq;
use Riak\Client\ProtoBuf\RpbBucketProps;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;
use Riak\Client\Core\Message\Bucket\GetRequest;
use Riak\Client\Core\Message\Bucket\GetResponse;

/**
 * rpb get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoGet extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbGetBucketReq
     */
    private function createRpbMessage(GetRequest $request)
    {
        $rpbGetReq = new RpbGetBucketReq();

        $rpbGetReq->setBucket($request->bucket);
        $rpbGetReq->setType($request->type);

        return $rpbGetReq;
    }

    /**
     * @param \Riak\Client\ProtoBuf\RpbBucketProps $props
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    private function createGetResponse(RpbBucketProps $props)
    {
        $response = new GetResponse();

        $response->r             = $this->decodeQuorum($props->r);
        $response->rw            = $this->decodeQuorum($props->rw);
        $response->w             = $this->decodeQuorum($props->w);
        $response->dw            = $this->decodeQuorum($props->dw);
        $response->pw            = $this->decodeQuorum($props->pw);
        $response->pr            = $this->decodeQuorum($props->pr);
        $response->nVal          = $props->n_val;
        $response->allowMult     = $props->allow_mult;
        $response->basicQuorum   = $props->basic_quorum;
        $response->bigVclock     = $props->big_vclock;
        $response->lastWriteWins = $props->last_write_wins;
        $response->notfoundOk    = $props->notfound_ok;
        $response->oldVclock     = $props->old_vclock;
        $response->smallVclock   = $props->small_vclock;
        $response->youngVclock   = $props->young_vclock;

        // optional values
        $response->search       = $props->search;
        $response->searchIndex  = $props->search_index;
        $response->backend      = $props->backend;
        $response->consistent   = $props->consistent;
        $response->datatype     = $props->datatype;

        if ($props->hasLinkfun()) {
            $response->linkwalkFunction = [
                'module'   => $props->linkfun->module,
                'function' => $props->linkfun->function
            ];
        }

        if ($props->hasChashKeyfun()) {
            $response->chashKeyFunction = [
                'module'   => $props->chash_keyfun->module,
                'function' => $props->chash_keyfun->function
            ];
        }

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $rpbGetReq  = $this->createRpbMessage($request);
        $rpbGetResp = $this->client->send($rpbGetReq, RiakMessageCodes::GET_BUCKET_REQ, RiakMessageCodes::GET_BUCKET_RESP);
        $rpbProps   = $rpbGetResp->getProps();

        return $this->createGetResponse($rpbProps);
    }
}
