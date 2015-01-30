<?php

namespace RiakClientTest\Core\Transport\Http\Search;

use Riak\Client\Core\Transport\Http\Search\HttpPutSchema;
use Riak\Client\Core\Message\Search\PutSchemaRequest;
use GuzzleHttp\Stream\Stream;
use RiakClientTest\TestCase;

class HttpPutSchemaTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Search\HttpPutSchema
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpPutSchema($this->client);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new PutSchemaRequest();
        $url        = '/search/schema/schema-name';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $callback   = function($subject) {
            $this->assertInstanceOf('GuzzleHttp\Stream\Stream', $subject);
            $this->assertEquals('schema-content', (string)$subject);

            return true;
        };

        $getRequest->name    = 'schema-name';
        $getRequest->content = 'schema-content';

        $request->expects($this->once())
            ->method('setBody')
            ->with($this->callback($callback));

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo($url))
            ->willReturn($request);

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testPutSchemaRequestContent()
    {
        $request      = new PutSchemaRequest();
        $body         = Stream::factory('schema-content');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');
        $callback     = function($subject) {
            $this->assertInstanceOf('GuzzleHttp\Stream\Stream', $subject);
            $this->assertEquals('schema-content', (string)$subject);

            return true;
        };

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('PUT'), $this->equalTo('/search/schema/schema-name'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
            ->method('setBody')
            ->with($this->callback($callback))
            ->willReturn($body);

        $request->name    = 'schema-name';
        $request->content = 'schema-content';

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutSchemaResponse', $this->instance->send($request));
    }
}