<?php

namespace RiakClientTest\Core\Transport\Http\Bucket;

use Riak\Client\Core\Transport\Http\Bucket\HttpList;
use Riak\Client\Core\Message\Bucket\ListRequest;
use RiakClientTest\TestCase;

class HttpListTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Bucket\HttpList
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpList($this->client);
    }

    public function testValidResponseCodes()
    {
        $codes = $this->getPropertyValue($this->instance, 'validResponseCodes');

        $this->assertArrayHasKey(200, $codes);
    }

    public function testCreateHttpRequest()
    {
        $url        = '/buckets';
        $getRequest = new ListRequest();
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $getRequest->timeout = 60;
        $getRequest->type    = null;

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

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

        $query->expects($this->exactly(2))
            ->method('add')
            ->will($this->returnValueMap([
                ['buckets', 'true', $query],
                ['timeout', 60, $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testListRequestContent()
    {
        $request      = new ListRequest();
        $url          = '/types/bucket_type/buckets';
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

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpResponse->expects($this->once())
            ->method('json')
            ->willReturn(['buckets' => [
                'bucket_name1',
                'bucket_name2',
                'bucket_name3'
          ]]);

        $httpResponse->method('getHeaders')
            ->willReturn([
                'Content-Type'  => 'application/json'
            ]);

        $httpQuery->expects($this->exactly(2))
            ->method('add')
            ->will($this->returnValueMap([
                ['buckets', 'true', $httpQuery],
                ['timeout', 120, $httpQuery],
            ]));

        $request->timeout = 120;
        $request->type    = 'bucket_type';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Bucket\ListResponse', $response);
        $this->assertInstanceOf('Iterator', $response->iterator);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Unexpected status code : "555"
     */
    public function testUnexpectedHttpStatusCode()
    {
        $request      = new ListRequest();
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

        $request->timeout = 60;
        $request->type    = null;

        $this->instance->send($request);
    }
}