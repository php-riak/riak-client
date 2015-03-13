<?php

namespace RiakClientTest\Core\Transport\Http\Kv;

use RiakClientTest\TestCase;
use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Transport\Http\Kv\HttpDelete;
use Riak\Client\Core\Message\Kv\DeleteRequest;

class HttpDeleteTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Kv\HttpDelete
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpDelete($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
        $this->assertArrayHasKey(204, $codes);
        $this->assertArrayHasKey(404, $codes);
    }

    public function testCreateDeleteHttpRequest()
    {
        $deleteRequest = new DeleteRequest();
        $url        = '/types/default/buckets/test_bucket/keys/1';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $deleteRequest->bucket = 'test_bucket';
        $deleteRequest->type   = 'default';
        $deleteRequest->key    = '1';

        $deleteRequest->r       = 1;
        $deleteRequest->pr      = 2;
        $deleteRequest->rw      = 3;
        $deleteRequest->w       = 3;
        $deleteRequest->dw      = 2;
        $deleteRequest->pw      = 1;
        $deleteRequest->vClock  = 'vclock-hash';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('DELETE'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $request->expects($this->exactly(2))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Accept', ['multipart/mixed', '*/*'], $query],
                ['X-Riak-Vclock', 'vclock-hash', $query],
            ]));

        $query->expects($this->exactly(6))
            ->method('add')
            ->will($this->returnValueMap([
                ['r', 1, $query],
                ['pr', 2, $query],
                ['rw', 3, $query],
                ['w', 3, $query],
                ['dw', 2, $query],
                ['pw', 1, $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$deleteRequest]));
    }

    public function testSendDeleteRequest()
    {
        $request      = new DeleteRequest();
        $stream       = Stream::factory('');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('DELETE'), $this->equalTo('/types/default/buckets/test_bucket/keys/1'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $httpResponse->method('getHeaders')
            ->willReturn([
                'Content-Type'  => 'application/json',
                'Content-Length' => '0',
            ]);

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Kv\DeleteResponse', $response);
    }

    /**
     * @expectedException \Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Unexpected status code : "999"
     */
    public function testUnexpectedStatusCodeException()
    {
        $request      = new DeleteRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('DELETE'), $this->equalTo('/types/default/buckets/test_bucket/keys/1'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(999);

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->instance->send($request);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Unexpected status code : "555"
     */
    public function testUnexpectedHttpStatusCode()
    {
        $request      = new DeleteRequest();
        $httpQuery    = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
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
        $request->key    = '1';

        $this->instance->send($request);
    }
}