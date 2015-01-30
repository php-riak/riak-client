<?php

namespace RiakClientTest\Core\Transport\Http\Search;

use Riak\Client\Core\Transport\Http\Search\HttpDeleteIndex;
use Riak\Client\Core\Message\Search\DeleteIndexRequest;
use RiakClientTest\TestCase;

class HttpDeleteIndexTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Search\HttpDeleteIndex
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpDeleteIndex($this->client);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new DeleteIndexRequest();
        $url        = '/search/index/index-name';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');

        $getRequest->name = 'index-name';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('DELETE'), $this->equalTo($url))
            ->willReturn($request);

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testDeleteIndexRequestContent()
    {
        $request      = new DeleteIndexRequest();
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('DELETE'), $this->equalTo('/search/index/index-name'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $request->name = 'index-name';

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\DeleteIndexResponse', $this->instance->send($request));
    }
}