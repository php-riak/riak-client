<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Bucket\ProtoGet;
use Riak\Client\Core\Message\Bucket\GetRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbGetBucketResp;
use Riak\Client\ProtoBuf\RpbBucketProps;
use RiakClientTest\TestCase;

class ProtoGetTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Bucket\ProtoGet
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoGet($this->client);
    }

    public function testCreateRpbMessage()
    {
        $getRequest = new GetRequest();

        $getRequest->bucket = 'test_bucket';
        $getRequest->type   = 'default';

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbGetBucketReq', $message);
        $this->assertEquals('test_bucket', $message->bucket);
        $this->assertEquals('default', $message->type);
    }

    public function testGetMessageResponse()
    {
        $rpbResp  = new RpbGetBucketResp();
        $rpbProps = new RpbBucketProps();
        $request  = new GetRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbGetBucketReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);

            return true;
        };

        $request->bucket = 'test_bucket';
        $request->type   = 'default';

        $rpbResp->setProps($rpbProps);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::GET_BUCKET_REQ),
                $this->equalTo(RiakMessageCodes::GET_BUCKET_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Bucket\GetResponse', $this->instance->send($request));
    }
}