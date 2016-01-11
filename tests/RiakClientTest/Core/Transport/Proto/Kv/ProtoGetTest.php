<?php

namespace RiakClientTest\Core\Transport\Proto\Kv;

use RiakClientTest\TestCase;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Transport\Proto\Kv\ProtoGet;
use Riak\Client\Core\Message\Kv\GetRequest;

class ProtoGetTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Kv\ProtoGet
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoGet($this->client);
    }

    public function testCreateGetProtoMessage()
    {
        $getRequest = new GetRequest();

        $getRequest->bucket = 'test_bucket';
        $getRequest->type   = 'default';
        $getRequest->key    = '1';

        $getRequest->r           = 3;
        $getRequest->pr          = 2;
        $getRequest->basicQuorum = true;
        $getRequest->notfoundOk  = true;

        $result = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbGetReq', $result);
        $this->assertEquals('test_bucket', $result->getBucket());
        $this->assertEquals('default', $result->getType());
        $this->assertEquals('1', $result->getKey());

        $this->assertEquals(3, $result->getR());
        $this->assertEquals(2, $result->getPr());
        $this->assertEquals(true, $result->getBasicQuorum());
        $this->assertEquals(true, $result->getNotfoundOk());
    }

    public function testSendGetMessage()
    {
        $request  = new GetRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbGetReq', $subject);
            $this->assertEquals('test_bucket', $subject->getBucket());
            $this->assertEquals('default', $subject->getType());
            $this->assertEquals('1', $subject->getKey());

            return true;
        };

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->callback($callback), $this->equalTo(RiakMessageCodes::GET_REQ), $this->equalTo(RiakMessageCodes::GET_RESP));

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->assertInstanceOf('Riak\Client\Core\Message\Kv\GetResponse', $this->instance->send($request));
    }
}