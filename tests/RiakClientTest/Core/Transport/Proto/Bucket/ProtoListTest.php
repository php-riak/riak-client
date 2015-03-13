<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Bucket\ProtoList;
use Riak\Client\Core\Message\Bucket\ListRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use RiakClientTest\TestCase;

class ProtoListTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Bucket\ProtoList
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoList($this->client);
    }

    public function testCreateRpbMessage()
    {
        $request = new ListRequest();

        $request->timeout = 60;
        $request->type    = null;

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbListBucketsReq', $message);
        $this->assertEquals(60, $message->timeout);
        $this->assertFalse($message->hasType());
    }

    public function testGetMessageResponse()
    {
        $socket   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $request  = new ListRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbListBucketsReq', $subject);
            $this->assertEquals('bucket_type', $subject->type);
            $this->assertEquals(120, $subject->timeout);

            return true;
        };

        $request->timeout = 120;
        $request->type    = 'bucket_type';

        $this->client->expects($this->once())
            ->method('emit')
            ->willReturn($socket)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::LIST_BUCKETS_REQ)
            );

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Bucket\ListResponse', $response);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Proto\Bucket\ProtoListResponseIterator', $response->iterator);
    }
}