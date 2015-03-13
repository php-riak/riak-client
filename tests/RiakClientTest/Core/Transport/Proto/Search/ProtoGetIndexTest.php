<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Search\ProtoGetIndex;
use Riak\Client\Core\Message\Search\GetIndexRequest;
use Riak\Client\ProtoBuf\RpbYokozunaIndexGetResp;
use Riak\Client\ProtoBuf\RpbYokozunaIndex;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use RiakClientTest\TestCase;

class ProtoGetIndexTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Search\ProtoGetIndex
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoGetIndex($this->client);
    }

    public function testCreateRpbMessage()
    {
        $getRequest = new GetIndexRequest();

        $getRequest->name = 'index-name';

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexGetReq', $message);
        $this->assertEquals('index-name', $message->name);
    }

    public function testGetMessageResponse()
    {
        $rpbResp  = new RpbYokozunaIndexGetResp();
        $rpbIndex = new RpbYokozunaIndex();
        $request  = new GetIndexRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexGetReq', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $request->name    = 'index-name';
        $rpbIndex->name   = 'index-name';
        $rpbIndex->schema = 'index-schema';

        $rpbResp->setIndex($rpbIndex);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_GET_REQ),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_GET_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetIndexResponse', $this->instance->send($request));
    }

    public function testGetMessageResponseNoIndex()
    {
        $rpbResp  = new RpbYokozunaIndexGetResp();
        $request  = new GetIndexRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexGetReq', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $request->name = 'index-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_GET_REQ),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_GET_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetIndexResponse', $this->instance->send($request));
    }

    public function testGetMessageResponseNull()
    {
        $request  = new GetIndexRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexGetReq', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $request->name = 'index-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn(null)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_GET_REQ),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_GET_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetIndexResponse', $this->instance->send($request));
    }
}