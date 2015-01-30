<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Bucket\ProtoPut;
use Riak\Client\Core\Message\Bucket\PutRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbPutBucketResp;
use Riak\Client\ProtoBuf\RpbBucketProps;
use RiakClientTest\TestCase;

class ProtoPutTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Bucket\ProtoPut
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoPut($this->client);
    }

    public function testCreateRpbMessage()
    {
        $getRequest = new PutRequest();

        $getRequest->bucket = 'test_bucket';
        $getRequest->type   = 'default';

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbSetBucketReq', $message);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbBucketProps', $message->props);
        $this->assertEquals('test_bucket', $message->bucket);
        $this->assertEquals('default', $message->type);
    }

    public function testGetMessageResponse()
    {
        $request  = new PutRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbSetBucketReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbBucketProps', $subject->props);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);

            return true;
        };

        $request->bucket = 'test_bucket';
        $request->type   = 'default';

        $this->client->expects($this->once())
            ->method('send')
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::SET_BUCKET_REQ),
                $this->equalTo(RiakMessageCodes::SET_BUCKET_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Bucket\PutResponse', $this->instance->send($request));
    }
}