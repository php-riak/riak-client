<?php

namespace RiakClientTest\Core\Transport\Http\Search;

use Riak\Client\Core\Transport\Http\Search\HttpGetSchema;
use Riak\Client\Core\Message\Search\GetSchemaRequest;
use GuzzleHttp\Stream\Stream;
use RiakClientTest\TestCase;

class HttpGetSchemaTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Search\HttpGetSchema
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpGetSchema($this->client);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new GetSchemaRequest();
        $url        = '/search/schema/schema-name';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');

        $getRequest->name = 'schema-name';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo($url))
            ->willReturn($request);

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testGetSchemaRequestContent()
    {
        $request      = new GetSchemaRequest();
        $body         = Stream::factory('schema-content');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/search/schema/schema-name'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($body);

        $request->name = 'schema-name';

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetSchemaResponse', $response);
        $this->assertEquals((string) $body, $response->content);
        $this->assertEquals('schema-name', $response->name);
    }
}