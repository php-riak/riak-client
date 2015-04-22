<?php

namespace RiakClientTest\Core\Transport\Proto\Kv;

use RiakClientTest\TestCase;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Message\Kv\ListKeysRequest;
use Riak\Client\Core\Transport\Proto\Kv\ProtoListKeys;

class ProtoListKeysTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Kv\ProtoListKeys
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoListKeys($this->client);
    }

    public function testCreateGetProtoMessage()
    {
        $request = new ListKeysRequest();

        $request->bucket  = 'test_bucket';
        $request->type    = 'default';
        $request->timeout = 120;

        $result = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbListKeysReq', $result);
        $this->assertEquals('test_bucket', $result->bucket);
        $this->assertEquals('default', $result->type);
        $this->assertEquals(120, $result->timeout);
    }

    public function testSendGetMessage()
    {
        $rpbStream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $request   = new ListKeysRequest();
        $callback  = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbListKeysReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals(120, $subject->timeout);

            return true;
        };

        $this->client->expects($this->once())
            ->method('emit')
            ->willReturn($rpbStream)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::LIST_KEYS_REQ)
            );

        $request->bucket  = 'test_bucket';
        $request->type    = 'default';
        $request->timeout = 120;

        $result = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Kv\ListKeysResponse', $result);
        $this->assertInstanceOf('Iterator', $result->iterator);
    }
}