<?php

namespace RiakClientTest\Core;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNodeBuilder;

class RiakNodeBuilderTest extends TestCase
{
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new RiakNodeBuilder();
    }

    public function testBuildHttpNode()
    {
        $node = $this->builder
            ->withProtocol('http')
            ->withHost('localhost')
            ->withPort('8098')
            ->build();

        $this->assertInstanceOf('Riak\Client\Core\RiakNode', $node);
        $this->assertInstanceOf('Riak\Client\Core\RiakHttpTransport', $node->getAdapter());
        $this->assertInstanceOf('GuzzleHttp\Client', $node->getAdapter()->getClient());

        $adpter  = $node->getAdapter();
        $client  = $adpter->getClient();
        $baseUrl = $client->getBaseUrl();
        $auth    = $client->getDefaultOption('auth');

        $this->assertEquals('http://localhost:8098', $baseUrl);
        $this->assertNull($auth);
    }

    public function testBuildProtoNode()
    {
        $node = $this->builder
            ->withProtocol('proto')
            ->withHost('localhost')
            ->withPort('8087')
            ->build();

        $this->assertInstanceOf('Riak\Client\Core\RiakNode', $node);
        $this->assertInstanceOf('Riak\Client\Core\RiakProtoTransport', $node->getAdapter());
        $this->assertInstanceOf('Riak\Client\Core\Transport\Proto\ProtoClient', $node->getAdapter()->getClient());
    }

    public function testBuildHttpNodeWithAuth()
    {
        $node = $this->builder
            ->withProtocol('https')
            ->withHost('localhost')
            ->withPort('8098')
            ->withUser('http_user')
            ->withPass('http_pass')
            ->build();

        $this->assertInstanceOf('Riak\Client\Core\RiakNode', $node);
        $this->assertInstanceOf('Riak\Client\Core\RiakHttpTransport', $node->getAdapter());
        $this->assertInstanceOf('GuzzleHttp\Client', $node->getAdapter()->getClient());

        $adpter  = $node->getAdapter();
        $client  = $adpter->getClient();
        $baseUrl = $client->getBaseUrl();
        $auth    = $client->getDefaultOption('auth');

        $this->assertEquals('https://localhost:8098', $baseUrl);
        $this->assertEquals(['http_user', 'http_pass'], $auth);
    }

    /**
     * @expectedException \Riak\Client\RiakException
     * @expectedExceptionMessage Unknown protocol : NOT_VALID
     */
    public function testBuildNodeInvalidProtocolException()
    {
        $this->builder
            ->withProtocol('NOT_VALID')
            ->withHost('localhost')
            ->withPort('8098')
            ->build();
    }
}