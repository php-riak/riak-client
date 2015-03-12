<?php

namespace Riak\Client\Core\Transport\Http\Bucket;

use Riak\Client\Core\Message\Request;
use Riak\Client\Core\Message\Bucket\GetRequest;
use Riak\Client\Core\Message\Bucket\GetResponse;
use Riak\Client\Core\Transport\RiakTransportException;

/**
 * Http get implementation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpGet extends BaseHttpStrategy
{
    /**
     * @var array
     */
    protected $validResponseCodes = [
        200 => true
    ];

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $getRequest
     *
     * @return \GuzzleHttp\Message\RequestInterface
     */
    private function createHttpRequest(GetRequest $getRequest)
    {
        $request = $this->createRequest('GET', $getRequest->type, $getRequest->bucket);

        $request->setHeader('Accept', 'application/json');

        return $request;
    }

    /**
     * @param \Riak\Client\Core\Message\Bucket\GetRequest $request
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function send(Request $request)
    {
        $httpRequest  = $this->createHttpRequest($request);
        $httpResponse = $this->client->send($httpRequest);
        $code         = $httpResponse->getStatusCode();

        if ( ! isset($this->validResponseCodes[$code])) {
            throw RiakTransportException::unexpectedStatusCode($code);
        }

        $json  = $httpResponse->json();
        $props = $json['props'];

        return $this->createGetResponse($props);
    }

    /**
     * @param array $props
     *
     * @return \Riak\Client\Core\Message\Bucket\GetResponse
     */
    public function createGetResponse(array $props)
    {
        $response = new GetResponse();

        $response->allowMult     = $props['allow_mult'];
        $response->basicQuorum   = $props['basic_quorum'];
        $response->bigVclock     = $props['big_vclock'];
        $response->dw            = $props['dw'];
        $response->lastWriteWins = $props['last_write_wins'];
        $response->notfoundOk    = $props['notfound_ok'];
        $response->nVal          = $props['n_val'];
        $response->oldVclock     = $props['old_vclock'];
        $response->pr            = $props['pr'];
        $response->pw            = $props['pw'];
        $response->r             = $props['r'];
        $response->rw            = $props['rw'];
        $response->w             = $props['w'];
        $response->smallVclock   = $props['small_vclock'];
        $response->youngVclock   = $props['young_vclock'];

        // optional values
        $response->search      = isset($props['search']) ? $props['search'] : null;
        $response->backend     = isset($props['backend']) ? $props['backend'] : null;
        $response->datatype    = isset($props['datatype']) ? $props['datatype'] : null;
        $response->consistent  = isset($props['consistent']) ? $props['consistent'] : null;
        $response->searchIndex = isset($props['search_index']) ? $props['search_index'] : null;

        return $response;
    }
}
