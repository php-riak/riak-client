<?php

namespace RiakClientTest\Core\Transport\Http\Kv;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Kv\ListKeysRequest;
use Riak\Client\Core\Transport\Http\Kv\HttpListKeys;

class HttpListKeysTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Kv\ProtoListKeys
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpListKeys($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
        $this->assertArrayHasKey(300, $codes);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new ListKeysRequest();
        $url        = '/types/bucket_type/buckets/bucket_name/keys';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $getRequest->bucket  = 'bucket_name';
        $getRequest->type    = 'bucket_type';
        $getRequest->timeout = 120;

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->once())
            ->method('setHeader')
            ->with(
                $this->equalTo('Accept'),
                $this->equalTo('application/json')
            );

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->exactly(2))
            ->method('add')
            ->will($this->returnValueMap([
                ['keys', 'true', $query],
                ['timeout', 120, $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testListKeysRequestContent()
    {
        $request      = new ListKeysRequest();
        $url          = '/types/bucket_type/buckets/bucket_name/keys';
        $httpQuery    = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo($url))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpResponse->expects($this->once())
            ->method('json')
            ->willReturn(['keys' => [
                '5536b4c644bed',
                '5536b8af46e29',
                '5536b67f45708'
          ]]);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $httpQuery->expects($this->exactly(2))
            ->method('add')
            ->will($this->returnValueMap([
                ['keys', 'true', $httpQuery],
                ['timeout', 120, $httpQuery],
            ]));

        $request->bucket  = 'bucket_name';
        $request->type    = 'bucket_type';
        $request->timeout = 120;

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Kv\ListKeysResponse', $response);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Unexpected status code : "555"
     */
    public function testUnexpectedHttpStatusCode()
    {
        $request      = new ListKeysRequest();
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

        $request->bucket  = 'bucket_name';
        $request->type    = 'bucket_type';
        $request->timeout = 120;

        $this->instance->send($request);
    }
}