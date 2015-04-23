<?php

namespace Riak\Client\Core\Transport\Proto\Bucket;

use Riak\Client\ProtoBuf\RpbModFun;
use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RpbCommitHook;
use Riak\Client\ProtoBuf\RpbGetBucketReq;
use Riak\Client\ProtoBuf\RpbBucketProps;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Message\Bucket\GetRequest;
use Riak\Client\Core\Message\Bucket\GetResponse;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;

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
            $response->linkwalkFunction = $this->parseRpbModFun($props->linkfun);
        }

        if ($props->hasChashKeyfun()) {
            $response->chashKeyFunction = $this->parseRpbModFun($props->chash_keyfun);
        }

        foreach ($props->getPrecommitList() as $hook) {
            $response->precommitHooks[] = $this->parseRpbCommitHook($hook);
        }

        foreach ($props->getPostcommitList() as $hook) {
            $response->postcommitHooks[] = $this->parseRpbCommitHook($hook);
        }

        return $response;
    }

    /**
     * @param \Riak\Client\ProtoBuf\RpbCommitHook $hook
     *
     * @return array
     */
    private function parseRpbCommitHook(RpbCommitHook $hook)
    {
        if ($hook->hasName()) {
            return ['name' => $hook->name];
        }

        return $this->parseRpbModFun($hook->modfun);
    }

    /**
     * @param \Riak\Client\ProtoBuf\RpbModFun $function
     *
     * @return array
     */
    private function parseRpbModFun(RpbModFun $function)
    {
        return [
            'module'   => $function->module,
            'function' => $function->function
        ];
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
        $response   = $this->createGetResponse($rpbProps);

        $response->name = $request->bucket;

        return $this->createGetResponse($rpbProps);
    }
}
