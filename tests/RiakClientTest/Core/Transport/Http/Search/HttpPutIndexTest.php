<?php

namespace RiakClientTest\Core\Transport\Http\Search;

use Riak\Client\Core\Transport\Http\Search\HttpPutIndex;
use Riak\Client\Core\Message\Search\PutIndexRequest;
use GuzzleHttp\Stream\Stream;
use RiakClientTest\TestCase;

class HttpPutIndexTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Search\HttpPutIndex
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpPutIndex($this->client);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new PutIndexRequest();
        $url        = '/search/index/index-name';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $callback   = function($subject) {
            $json = json_decode($subject, true);

            $this->assertEquals('schema-content', $json['schema']);
            $this->assertEquals(3, $json['n_val']);

            return true;
        };

        $getRequest->nVal   = 3;
        $getRequest->name   = 'index-name';
        $getRequest->schema = 'schema-content';

        $request->expects($this->once())
            ->method('setBody')
            ->with($this->callback($callback));

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo($url))
            ->willReturn($request);

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testPutIndexRequestContent()
    {
        $request      = new PutIndexRequest();
        $body         = Stream::factory('index-content');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $callback     = function($subject) {
            $json = json_decode($subject, true);

            $this->assertEquals('schema-content', $json['schema']);
            $this->assertEquals(3, $json['n_val']);

            return true;
        };

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo('/search/index/index-name'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
            ->method('setBody')
            ->with($this->callback($callback))
            ->willReturn($body);

        $request->nVal   = 3;
        $request->name   = 'index-name';
        $request->schema = 'schema-content';

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutIndexResponse', $this->instance->send($request));
    }
}