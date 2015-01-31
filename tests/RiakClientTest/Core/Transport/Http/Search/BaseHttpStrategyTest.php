<?php

namespace RiakClientTest\Core\Transport\Http\Search;

use RiakClientTest\TestCase;

class BaseHttpStrategyTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Search\BaseHttpStrategy
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = $this->getMockForAbstractClass(
            'Riak\Client\Core\Transport\Http\Search\BaseHttpStrategy',
            [$this->client], '', true, true, true, ['send']
        );
    }

    public function testBuildPath()
    {
        $this->assertEquals('/search/index/index-name', $this->invokeMethod($this->instance, 'buildPath', ['index', 'index-name']));
        $this->assertEquals('/search/schema/schema-name', $this->invokeMethod($this->instance, 'buildPath', ['schema', 'schema-name']));
        $this->assertEquals('/search/query/search-index', $this->invokeMethod($this->instance, 'buildPath', ['query', 'search-index']));
    }

    public function testCreateSchemaRequest()
    {
        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/search/schema/schema-name'));

        $this->invokeMethod($this->instance, 'createSchemaRequest', ['GET' , 'schema-name']);
    }

    public function testCreateIndexRequest()
    {
        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/search/index/index-name'));

        $this->invokeMethod($this->instance, 'createIndexRequest', ['GET' , 'index-name']);
    }
}