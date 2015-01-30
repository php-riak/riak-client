<?php

namespace RiakClientTest\Core\Transport\Proto\Kv;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Kv\Content;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Transport\Proto\Kv\ProtoPut;
use Riak\Client\Core\Message\Kv\PutRequest;

class ProtoPutTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Kv\ProtoPut
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoPut($this->client);
    }

    public function testCreatePutProtoMessage()
    {
        $content    = new Content();
        $putRequest = new PutRequest();

        $putRequest->bucket = 'test_bucket';
        $putRequest->type   = 'default';
        $putRequest->key    = '1';

        $putRequest->w           = 3;
        $putRequest->pw          = 2;
        $putRequest->dw          = 1;
        $putRequest->returnBody  = true;
        $putRequest->content     = $content;
        $putRequest->vClock      = 'vclock-hash';

        $content->contentType = 'application/json';
        $content->value       = '[1,1,1]';

        $result = $this->invokeMethod($this->instance, 'createRpbMessage', [$putRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbPutReq', $result);
        $this->assertEquals('test_bucket', $result->bucket);
        $this->assertEquals('default', $result->type);
        $this->assertEquals('1', $result->key);

        $this->assertEquals(3, $result->w);
        $this->assertEquals(2, $result->pw);
        $this->assertEquals(1, $result->dw);
        $this->assertEquals(true, $result->return_body);
        $this->assertEquals('[1,1,1]', $result->content->value);
        $this->assertEquals('application/json', $result->content->content_type);
    }

    public function testSendPutMessage()
    {
        $request  = new PutRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbPutReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->callback($callback), $this->equalTo(RiakMessageCodes::PUT_REQ), $this->equalTo(RiakMessageCodes::PUT_RESP));

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->assertInstanceOf('Riak\Client\Core\Message\Kv\PutResponse', $this->instance->send($request));
    }
}