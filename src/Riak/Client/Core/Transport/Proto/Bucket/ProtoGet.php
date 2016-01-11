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

        $response->r             = $this->decodeQuorum($props->getR());
        $response->rw            = $this->decodeQuorum($props->getRw());
        $response->w             = $this->decodeQuorum($props->getW());
        $response->dw            = $this->decodeQuorum($props->getDw());
        $response->pw            = $this->decodeQuorum($props->getPw());
        $response->pr            = $this->decodeQuorum($props->getPr());
        $response->nVal          = $props->getNVal();
        $response->allowMult     = $props->getAllowMult();
        $response->basicQuorum   = $props->getBasicQuorum();
        $response->bigVclock     = $props->getBigVclock();
        $response->lastWriteWins = $props->getLastWriteWins();
        $response->notfoundOk    = $props->getnotfoundOk();
        $response->oldVclock     = $props->getOldVclock();
        $response->smallVclock   = $props->getSmallVclock();
        $response->youngVclock   = $props->getYoungVclock();

        // optional values
        $response->search       = $props->getSearch();
        $response->searchIndex  = $props->getSearchIndex();
        $response->backend      = $props->getBackend();
        $response->consistent   = $props->getConsistent();
        $response->datatype     = $props->getDatatype();

        if ($props->hasLinkfun()) {
            $response->linkwalkFunction = $this->parseRpbModFun($props->getLinkfun());
        }

        if ($props->hasChashKeyfun()) {
            $response->chashKeyFunction = $this->parseRpbModFun($props->getChashKeyfun());
        }

        if ($props->hasPrecommitList()) {
            foreach ($props->getPrecommitList() as $hook) {
                $response->precommitHooks[] = $this->parseRpbCommitHook($hook);
            }
        }

        if ($props->hasPostcommitList()) {
            foreach ($props->getPostcommitList() as $hook) {
                $response->postcommitHooks[] = $this->parseRpbCommitHook($hook);
            }
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
            return ['name' => $hook->getName()];
        }

        return $this->parseRpbModFun($hook->getModfun());
    }

    /**
     * @param \Riak\Client\ProtoBuf\RpbModFun $function
     *
     * @return array
     */
    private function parseRpbModFun(RpbModFun $function)
    {
        return [
            'module'   => $function->getModule(),
            'function' => $function->getFunction()
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
