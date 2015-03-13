<?php

namespace RiakClientTest\Core\Transport\Http\Bucket;

use Riak\Client\Core\Transport\Http\Bucket\HttpGet;
use Riak\Client\Core\Message\Bucket\GetRequest;
use RiakClientTest\TestCase;

class HttpGetTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Bucket\HttpGet
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
        $url        = '/types/default/buckets/test_bucket/props';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');

        $getRequest->bucket = 'test_bucket';
        $getRequest->type   = 'default';

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

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testGetRequestContent()
    {
        $request      = new GetRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/default/buckets/test_bucket/props'))
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
            ->willReturn([ 'props' => [
                'allow_mult'      => 'allow_mult',
                'backend'         => 'backend',
                'basic_quorum'    => 'basic_quorum',
                'big_vclock'      => 'big_vclock',
                'consistent'      => 'consistent',
                'datatype'        => 'datatype',
                'dw'              => 'dw',
                'last_write_wins' => 'last_write_wins',
                'notfound_ok'     => 'notfound_ok',
                'n_val'           => 'n_val',
                'old_vclock'      => 'old_vclock',
                'pr'              => 'pr',
                'pw'              => 'pw',
                'r'               => 'r',
                'rw'              => 'rw',
                'w'               => 'w',
                'search'          => 'search',
                'search_index'    => 'search_index',
                'small_vclock'    => 'small_vclock',
                'young_vclock'    => 'young_vclock',
          ]]);

        $httpResponse->method('getHeaders')
            ->willReturn([
                'Content-Type'  => 'application/json'
            ]);

        $request->bucket = 'test_bucket';
        $request->type   = 'default';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Bucket\GetResponse', $response);
        $this->assertEquals('allow_mult', $response->allowMult);
        $this->assertEquals('search', $response->search);
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