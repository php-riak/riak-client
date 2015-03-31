<?php

namespace Riak\Client\Core\Transport\Proto\Bucket;

use Riak\Client\ProtoBuf\RpbModFun;
use Riak\Client\Core\Message\Request;
use Riak\Client\ProtoBuf\RpbCommitHook;
use Riak\Client\ProtoBuf\RpbBucketProps;
use Riak\Client\ProtoBuf\RpbSetBucketReq;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Message\Bucket\PutRequest;
use Riak\Client\Core\Message\Bucket\PutResponse;
use Riak\Client\Core\Transport\Proto\ProtoStrategy;

/**
 * rpb put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoPut extends ProtoStrategy
{
    /**
     * @param \Riak\Client\Core\Message\Bucket\PutRequest $request
     *
     * @return \Riak\Client\ProtoBuf\RpbSetBucketReq
     */
    private function createRpbMessage(PutRequest $request)
    {
        $rpbPutReq = new RpbSetBucketReq();
        $rpbProps  = new RpbBucketProps();

        $rpbProps->r               = $this->encodeQuorum($request->r);
        $rpbProps->w               = $this->encodeQuorum($request->w);
        $rpbProps->dw              = $this->encodeQuorum($request->dw);
        $rpbProps->rw              = $this->encodeQuorum($request->rw);
        $rpbProps->pr              = $this->encodeQuorum($request->pr);
        $rpbProps->pw              = $this->encodeQuorum($request->pw);
        $rpbProps->n_val           = $request->nVal;
        $rpbProps->allow_mult      = $request->allowMult;
        $rpbProps->last_write_wins = $request->lastWriteWins;
        $rpbProps->old_vclock      = $request->oldVclock;
        $rpbProps->young_vclock    = $request->youngVclock;
        $rpbProps->big_vclock      = $request->bigVclock;
        $rpbProps->small_vclock    = $request->smallVclock;
        $rpbProps->basic_quorum    = $request->basicQuorum;
        $rpbProps->notfound_ok     = $request->notfoundOk;
        $rpbProps->backend         = $request->backend;
        $rpbProps->search          = $request->search;
        $rpbProps->search_index    = $request->searchIndex;
        $rpbProps->datatype        = $request->datatype;
        $rpbProps->consistent      = $request->consistent;

        if ($request->linkwalkFunction) {
            $rpbProps->setLinkfun($this->createRpbModFun($request->linkwalkFunction));
        }

        if ($request->chashKeyFunction) {
            $rpbProps->setChashKeyfun($this->createRpbModFun($request->chashKeyFunction));
        }

        foreach ($request->precommitHooks as $hook) {
            $rpbProps->addPrecommit($this->createRpbCommitHook($hook));
        }

        foreach ($request->postcommitHooks as $hook) {
            $rpbProps->addPostcommit($this->createRpbCommitHook($hook));
        }

        $rpbPutReq->setBucket($request->bucket);
        $rpbPutReq->setType($request->type);
        $rpbPutReq->setProps($rpbProps);

        return $rpbPutReq;
    }

    /**
     * @param array $hook
     *
     * @return \Riak\Client\ProtoBuf\RpbCommitHook
     */
    private function createRpbCommitHook(array $hook)
    {
        $func = new RpbCommitHook();

        if (isset($hook['name'])) {
            $func->setName($hook['name']);

            return $func;
        }

        $func->setModfun($this->createRpbModFun($hook));

        return $func;
    }

    /**
     * @param array $function
     *
     * @return \Riak\Client\ProtoBuf\RpbModFun
     */
    private function createRpbModFun(array $function)
    {
        $func = new RpbModFun();

        $func->setModule($function['module']);
        $func->setFunction($function['function']);

        return $func;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\PutRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\PutResponse
     */
    public function send(Request $request)
    {
        $response   = new PutResponse();
        $rpbPutReq  = $this->createRpbMessage($request);

        $this->client->send($rpbPutReq, RiakMessageCodes::SET_BUCKET_REQ, RiakMessageCodes::SET_BUCKET_RESP);

        return $response;
    }
}
