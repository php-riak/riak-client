<?php

namespace RiakClientTest\Core\Transport\Http\DataType;

use Riak\Client\Core\Transport\Http\DataType\HttpGet;
use Riak\Client\Core\Message\DataType\GetRequest;
use GuzzleHttp\Exception\ClientException;
use RiakClientTest\TestCase;

class HttpGetTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\DataType\HttpGet
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpGet($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new GetRequest();
        $url        = '/types/default/buckets/test_bucket/datatypes/1';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $getRequest->bucket = 'test_bucket';
        $getRequest->type   = 'default';
        $getRequest->key    = '1';

        $getRequest->r           = 3;
        $getRequest->pr          = 3;
        $getRequest->basicQuorum = true;
        $getRequest->notfoundOk  = true;

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

        $query->expects($this->exactly(4))
            ->method('add')
            ->will($this->returnValueMap([
                ['r', 3, $query],
                ['pr', 3, $query],
                ['basic_quorum', 'true', $query],
                ['notfound_ok', 'true', $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testGetRequestContent()
    {
        $request      = new GetRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/default/buckets/test_bucket/datatypes/1'))
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
            ->willReturn([
                'type'  => 'counter',
                'value' => 10,
            ]);

        $httpResponse->method('getHeaders')
            ->willReturn([
                'Content-Type'  => 'application/json'
            ]);

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\GetResponse', $response);
        $this->assertEquals('counter', $response->type);
        $this->assertEquals(10, $response->value);
    }

    public function testGetRequestHandl404ExceptioThrownByGuzzle()
    {
        $request      = new GetRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $httpQuery    = $this->getMock('GuzzleHttp\Query');

        $request->notfoundOk  = true;

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/default/buckets/test_bucket/datatypes/1'))
            ->willReturn($httpRequest);

        $httpResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willThrowException(new ClientException('Not Found', $httpRequest, $httpResponse));

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\GetResponse', $response);
        $this->assertNull($response->value);
        $this->assertNull($response->type);
    }

    /**
     * @expectedException GuzzleHttp\Exception\ClientException
     * @expectedExceptionMessage Request Exception
     */
    public function testGetRequestThrownRequestException()
    {
        $request      = new GetRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $httpQuery    = $this->getMock('GuzzleHttp\Query');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/default/buckets/test_bucket/datatypes/1'))
            ->willReturn($httpRequest);

        $httpResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(404);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willThrowException(new ClientException('Request Exception', $httpRequest, $httpResponse));

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
        $request      = new GetRequest();
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