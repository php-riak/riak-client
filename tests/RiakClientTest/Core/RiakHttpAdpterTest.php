<?php

namespace RiakClientTest\Core;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakHttpTransport;
use GuzzleHttp\Exception\ClientException;
use Riak\Client\Core\Message\Kv\GetRequest;
use Riak\Client\Core\Message\Kv\PutRequest;
use Riak\Client\Core\Message\Kv\DeleteRequest;

class RiakHttpAdpterTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\RiakHttpTransport
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new RiakHttpTransport($this->client);
    }

    public function testCreateAdapterStrategy()
    {
        $get    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new GetRequest()]);
        $put    = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new PutRequest()]);
        $delete = $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [new DeleteRequest()]);

        $this->assertInstanceOf('Riak\Client\Core\Transport\Http\Kv\HttpGet', $get);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Http\Kv\HttpPut', $put);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Http\Kv\HttpDelete', $delete);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Not Found
     * @expectedExceptionCode 404
     */
    public function testWrappedExceptioThrownByGuzzle()
    {
        $request      = new GetRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $httpQuery    = $this->getMock('GuzzleHttp\Query');

        $request->notfoundOk = false;

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/default/buckets/test_bucket/keys/1'))
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

        $this->instance->send($request);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnknownMessageException()
    {
        $mock = $this->getMock('Riak\Client\Core\Message\Request');

        $this->invokeMethod($this->instance, 'createAdapterStrategyFor', [$mock]);
    }
}