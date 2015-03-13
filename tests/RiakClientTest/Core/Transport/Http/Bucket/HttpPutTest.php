<?php

namespace RiakClientTest\Core\Transport\Http\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\Transport\Http\Bucket\HttpPut;
use Riak\Client\Core\Message\Bucket\PutRequest;

class HttpPutTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Bucket\HttpPut
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpPut($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
        $this->assertArrayHasKey(204, $codes);
    }

    public function testCreateHttpPutRequest()
    {
        $putRequest = new PutRequest();
        $url        = '/types/default/buckets/test_bucket/props';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');
        $callback   = function ($body) {
            $data   = json_decode($body, true);
            $props  = $data['props'];

            $this->assertEquals('allow_mult', $props['allow_mult']);
            $this->assertEquals('basic_quorum', $props['basic_quorum']);
            $this->assertEquals('big_vclock', $props['big_vclock']);
            $this->assertEquals('dw', $props['dw']);
            $this->assertEquals('last_write_wins', $props['last_write_wins']);
            $this->assertEquals('notfound_ok', $props['notfound_ok']);
            $this->assertEquals('n_val', $props['n_val']);
            $this->assertEquals('old_vclock', $props['old_vclock']);
            $this->assertEquals('pr', $props['pr']);
            $this->assertEquals('pw', $props['pw']);
            $this->assertEquals('r', $props['r']);
            $this->assertEquals('rw', $props['rw']);
            $this->assertEquals('w', $props['w']);
            $this->assertEquals('small_vclock', $props['small_vclock']);
            $this->assertEquals('young_vclock', $props['young_vclock']);

            return true;
        };

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';

        $putRequest->allowMult     = 'allow_mult';
        $putRequest->basicQuorum   = 'basic_quorum';
        $putRequest->bigVclock     = 'big_vclock';
        $putRequest->dw            = 'dw';
        $putRequest->lastWriteWins = 'last_write_wins';
        $putRequest->notfoundOk    = 'notfound_ok';
        $putRequest->nVal          = 'n_val';
        $putRequest->oldVclock     = 'old_vclock';
        $putRequest->pr            = 'pr';
        $putRequest->pw            = 'pw';
        $putRequest->r             = 'r';
        $putRequest->rw            = 'rw';
        $putRequest->w             = 'w';
        $putRequest->smallVclock   = 'small_vclock';
        $putRequest->youngVclock   = 'young_vclock';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->exactly(2))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Accept', 'multipart/json', $query],
                ['Content-Type', 'application/json', $query],
            ]));

        $request->expects($this->once())
            ->method('setBody')
            ->with($this->callback($callback));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$putRequest]));
    }

    public function testPutRequestBody()
    {
        $putRequest   = new PutRequest();
        $query        = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo('/types/default/buckets/test_bucket/props'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpRequest->expects($this->exactly(2))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Accept', 'multipart/json', $query],
                ['Content-Type', 'application/json', $query],
            ]));

        $httpRequest->expects($this->once())
            ->method('setBody')
            ->with($this->callback(function ($body) {
                $this->assertArrayHasKey('props', json_decode($body, true));

                return true;
            }));

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';

        $response = $this->instance->send($putRequest);

        $this->assertInstanceOf('Riak\Client\Core\Message\Bucket\PutResponse', $response);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Unexpected status code : "555"
     */
    public function testUnexpectedHttpStatusCode()
    {
        $request      = new PutRequest();
        $httpQuery    = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($httpResponse);

        $httpRequest->expects($this->any())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $httpQuery->expects($this->any())
            ->method('add')
            ->willReturn($httpRequest);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(555);

        $request->bucket = 'test_bucket';
        $request->type   = 'default';

        $this->instance->send($request);
    }
}