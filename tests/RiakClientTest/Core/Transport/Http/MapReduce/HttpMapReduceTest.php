<?php

namespace RiakClientTest\Core\Transport\Http\MapReduce;

use RiakClientTest\TestCase;
use GuzzleHttp\Stream\Stream;
use Riak\Client\Core\Message\MapReduce\MapReduceRequest;
use Riak\Client\Core\Transport\Http\MapReduce\HttpMapReduce;

class HttpMapReduceTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\MapReduce\HttpMapReduce
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpMapReduce($this->client);
    }

    public function testCreateRequest()
    {
        $request   = new MapReduceRequest();
        $httpQuery = $this->getMock('GuzzleHttp\Query');
        $httpReq   = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $mapred    = '{"inputs":"test", "query":[{"link":{"bucket":"test"}},{"map":{"language":"javascript","name":"Riak.mapValuesJson"}}]}';

        $request->request = $mapred;

        $httpReq->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $this->client->expects($this->once())
            ->method('createRequest')
            ->willReturn($httpReq)
            ->with($this->equalTo('POST'), $this->equalTo('/mapred'));

        $httpQuery->expects($this->once())
            ->method('add')
            ->willReturn($httpReq)
            ->with($this->equalTo('chunked'), $this->equalTo('true'));

        $httpReq->expects($this->exactly(2))
            ->method('setHeader')
            ->will($this->returnValueMap([
                ['Content-Type', 'application/json', $request],
                ['Accept', 'application/json', $request],
            ]));

        $this->assertSame($httpReq, $this->invokeMethod($this->instance, 'createHttpRequest', [$request]));
    }

    public function testGetRequestContent()
    {
        $stream       = Stream::factory();
        $request      = new MapReduceRequest();
        $httpQuery    = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $mapred       = '{"inputs":"test", "query":[{"link":{"bucket":"test"}},{"map":{"language":"javascript","name":"Riak.mapValuesJson"}}]}';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('POST'), $this->equalTo('/mapred'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $httpQuery->expects($this->once())
            ->method('add')
            ->willReturn($httpRequest)
            ->with($this->equalTo('chunked'), $this->equalTo('true'));

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $httpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $httpResponse->method('getHeader')
            ->will($this->returnValueMap([
                ['Content-Type', 'multipart/mixed; boundary=KQLFjHN3yt2P0CWSxcIywUeI0kR']
            ]));

        $request->request = $mapred;

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\MapReduce\MapReduceResponse', $response);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Http\MapReduce\HttpMapReduceResponseIterator', $response->iterator);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Unexpected status code : "555"
     */
    public function testUnexpectedHttpStatusCode()
    {
        $request      = new MapReduceRequest();
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

        $httpQuery->expects($this->once())
            ->method('add')
            ->willReturn($httpRequest);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(555);

        $request->request = '';

        $this->instance->send($request);
    }
}