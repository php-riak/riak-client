<?php

namespace RiakClientTest\Core\Transport\Http\DataType;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\Crdt\Op\CounterOp;
use Riak\Client\Core\Transport\Http\DataType\HttpPut;
use Riak\Client\Core\Message\DataType\PutRequest;

class HttpPutTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\DataType\HttpPut
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
    }

    public function testCreateHttpPutRequest()
    {
        $putRequest = new PutRequest();
        $url        = '/types/default/buckets/test_bucket/datatypes/1';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';
        $putRequest->key    = '1';

        $putRequest->w          = 3;
        $putRequest->pw         = 2;
        $putRequest->dw         = 1;
        $putRequest->returnBody = true;
        $putRequest->op         = new CounterOp(10);

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('POST'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->exactly(2))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Accept', 'multipart/json', $query],
                ['Content-Type', 'application/json', $query],
            ]));

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $request->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo('10'));

        $query->expects($this->exactly(4))
            ->method('add')
            ->will($this->returnValueMap([
                ['w', 1, $query],
                ['dw', 3, $query],
                ['pw', 2, $query],
                ['returnbody', 'true', $query]
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$putRequest]));
    }

    public function testGetRequestContent()
    {
        $putRequest   = new PutRequest();
        $query        = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('POST'), $this->equalTo('/types/default/buckets/test_bucket/datatypes/1'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpResponse->expects($this->once())
            ->method('json')
            ->willReturn([
                'type'  => 'counter',
                'value' => 10,
            ]);

        $httpRequest->expects($this->exactly(2))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Accept', 'multipart/json', $query],
                ['Content-Type', 'application/json', $query],
            ]));

        $httpRequest->expects($this->once())
            ->method('setBody')
            ->with($this->equalTo('10'));

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';
        $putRequest->key    = '1';

        $putRequest->returnBody = true;
        $putRequest->op         = new CounterOp(10);


        $response = $this->instance->send($putRequest);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\PutResponse', $response);
        $this->assertEquals('counter', $response->type);
        $this->assertEquals(10, $response->value);
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

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $httpQuery->expects($this->any())
            ->method('add')
            ->willReturn($httpRequest);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(555);

        $request->op     = new CounterOp(10);
        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->instance->send($request);
    }
}