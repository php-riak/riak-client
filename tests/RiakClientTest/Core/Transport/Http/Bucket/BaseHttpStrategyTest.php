<?php

namespace RiakClientTest\Core\Transport\Http\Bucket;

use RiakClientTest\TestCase;

class BaseHttpStrategyTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Bucket\BaseHttpStrategy
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = $this->getMockForAbstractClass(
            'Riak\Client\Core\Transport\Http\Bucket\BaseHttpStrategy',
            [$this->client], '', true, true, true, ['send']
        );
    }

    public function testBuildPath()
    {
        $this->assertEquals('/types/type/buckets/bucket/props', $this->invokeMethod($this->instance, 'buildPath', ['type', 'bucket', 'key']));
        $this->assertEquals('/buckets/bucket/props', $this->invokeMethod($this->instance, 'buildPath', [null, 'bucket', 'key']));
    }

    public function testCreateRequest()
    {
        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/types/type/buckets/bucket/props'));

        $this->invokeMethod($this->instance, 'createRequest', ['GET' , 'type', 'bucket', 'key']);
    }
}