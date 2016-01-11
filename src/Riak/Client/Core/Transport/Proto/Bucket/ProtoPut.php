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

        $rpbProps->setR($this->encodeQuorum($request->r));
        $rpbProps->setW($this->encodeQuorum($request->w));
        $rpbProps->setDw($this->encodeQuorum($request->dw));
        $rpbProps->setRw($this->encodeQuorum($request->rw));
        $rpbProps->setPr($this->encodeQuorum($request->pr));
        $rpbProps->setPw($this->encodeQuorum($request->pw));
        $rpbProps->setNVal($request->nVal);
        $rpbProps->setAllowMult($request->allowMult);
        $rpbProps->setLastWriteWins($request->lastWriteWins);
        $rpbProps->setOldVclock($request->oldVclock);
        $rpbProps->setYoungVclock($request->youngVclock);
        $rpbProps->setBigVclock($request->bigVclock);
        $rpbProps->setSmallVclock($request->smallVclock);
        $rpbProps->setBasicQuorum($request->basicQuorum);
        $rpbProps->setNotfoundOk($request->notfoundOk);
        $rpbProps->setBackend($request->backend);
        $rpbProps->setSearch($request->search);
        $rpbProps->setSearchIndex($request->searchIndex);
        $rpbProps->setDatatype($request->datatype);
        $rpbProps->setConsistent($request->consistent);

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
