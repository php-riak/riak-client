<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Search\ProtoPutIndex;
use Riak\Client\Core\Message\Search\PutIndexRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbPutResp;
use RiakClientTest\TestCase;

class ProtoPutIndexTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Search\ProtoPutIndex
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoPutIndex($this->client);
    }

    public function testCreateRpbMessage()
    {
        $request = new PutIndexRequest();

        $request->name   = 'index-name';
        $request->schema = 'schema-name';

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexPutReq', $message);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndex', $message->index);
        $this->assertEquals('schema-name', $message->index->schema);
        $this->assertEquals('index-name', $message->index->name);
    }

    public function testPutMessageResponse()
    {
        $rpbResp  = new RpbPutResp();
        $request  = new PutIndexRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexPutReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndex', $subject->index);
            $this->assertEquals('schema-name', $subject->index->schema);
            $this->assertEquals('index-name', $subject->index->name);

            return true;
        };

        $request->name   = 'index-name';
        $request->schema = 'schema-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_PUT_REQ),
                $this->equalTo(RiakMessageCodes::PUT_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutIndexResponse', $this->instance->send($request));
    }
}