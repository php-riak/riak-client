<?php

namespace RiakClientTest\Core\Transport\Http\Search;

use Riak\Client\Core\Transport\Http\Search\HttpGetIndex;
use Riak\Client\Core\Message\Search\GetIndexRequest;
use RiakClientTest\TestCase;

class HttpGetIndexTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Search\HttpGetIndex
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpGetIndex($this->client);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new GetIndexRequest();
        $url        = '/search/index/index-name';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');

        $getRequest->name = 'index-name';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo($url))
            ->willReturn($request);

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testGetIndexRequestContent()
    {
        $request      = new GetIndexRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/search/index/index-name'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpResponse->expects($this->once())
            ->method('json')
            ->willReturn([
                'n_val'  => 3,
                'name'   => 'index-name',
                'schema' => 'schema-name',
            ]);

        $request->name = 'index-name';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetIndexResponse', $response);
        $this->assertEquals('schema-name', $response->schema);
        $this->assertEquals('index-name', $response->name);
        $this->assertEquals(3, $response->nVal);
    }
}