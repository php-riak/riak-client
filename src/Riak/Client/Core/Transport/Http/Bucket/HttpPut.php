<?php

namespace Riak\Client\Core\Transport\Http\Bucket;

use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Bucket\PutRequest;
use Riak\Client\Core\Message\Bucket\PutResponse;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http put implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpPut extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true,
        204 => true,
    ];

    /**
     * @param \Riak\Client\Core\Message\Bucket\PutRequest $putRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(PutRequest $putRequest)
    {
        $request = $this->createRequest('PUT', $putRequest->type, $putRequest->bucket);
        $props   = $this->createDataMessage($putRequest);

        $request->setHeader('Accept', 'application/json');
        $request->setHeader('Content-Type', 'application/json');
        $request->setBody(Stream::factory(json_encode([
            'props' => $props
        ])));

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\PutRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\PutResponse
     */
    public function send(Request $request)
    {
        $response     = new PutResponse();
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Message\Request $request
     *
     * @return \Riak\Client\Core\Message\Request
     */
    private function createDataMessage(Request $request)
    {
        $data = array_filter([
            'dw'              => $request->dw,
            'n_val'           => $request->nVal,
            'pr'              => $request->pr,
            'pw'              => $request->pw,
            'r'               => $request->r,
            'rw'              => $request->rw,
            'w'               => $request->w,
            'allow_mult'      => $request->allowMult,
            'backend'         => $request->backend,
            'basic_quorum'    => $request->basicQuorum,
            'big_vclock'      => $request->bigVclock,
            'consistent'      => $request->consistent,
            'datatype'        => $request->datatype,
            'last_write_wins' => $request->lastWriteWins,
            'notfound_ok'     => $request->notfoundOk,
            'old_vclock'      => $request->oldVclock,
            'search'          => $request->search,
            'search_index'    => $request->searchIndex,
            'small_vclock'    => $request->smallVclock,
            'young_vclock'    => $request->youngVclock,
        ]);

        if ($request->linkwalkFunction) {
            $data['linkfun'] = $this->createFuncData($request->linkwalkFunction);
        }

        if ($request->chashKeyFunction) {
            $data['chash_keyfun'] = $this->createFuncData($request->chashKeyFunction);
        }

        foreach ($request->precommitHooks as $hook) {
            $data['precommit'][] = $this->createFuncData($hook);
        }

        foreach ($request->postcommitHooks as $hook) {
            $data['postcommit'][] = $this->createFuncData($hook);
        }

        return $data;
    }

    /**
     * @param array $hook
     *
     * @return array
     */
    private function createFuncData(array $hook)
    {
        if (isset($hook['name'])) {
            return ['name' => $hook['name']];
        }

        return [
            'mod'  => $hook['module'],
            'fun' => $hook['function']
        ];
    }
}
