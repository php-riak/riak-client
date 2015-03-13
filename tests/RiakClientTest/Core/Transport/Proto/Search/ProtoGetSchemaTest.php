<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Search\ProtoGetSchema;
use Riak\Client\Core\Message\Search\GetSchemaRequest;
use Riak\Client\ProtoBuf\RpbYokozunaSchemaGetResp;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use RiakClientTest\TestCase;

class ProtoGetSchemaTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Search\ProtoGetSchema
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoGetSchema($this->client);
    }

    public function testCreateRpbMessage()
    {
        $getRequest = new GetSchemaRequest();

        $getRequest->name = 'schema-name';

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaSchemaGetReq', $message);
        $this->assertEquals('schema-name', $message->name);
    }

    public function testGetMessageResponse()
    {
        $rpbResp  = new RpbYokozunaSchemaGetResp();
        $request  = new GetSchemaRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaSchemaGetReq', $subject);
            $this->assertEquals('schema-name', $subject->name);

            return true;
        };

        $request->name = 'schema-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_SCHEMA_GET_REQ),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_SCHEMA_GET_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetSchemaResponse', $this->instance->send($request));
    }

    public function testGetMessageResponseNull()
    {
        $request  = new GetSchemaRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaSchemaGetReq', $subject);
            $this->assertEquals('schema-name', $subject->name);

            return true;
        };

        $request->name = 'schema-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn(null)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_SCHEMA_GET_REQ),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_SCHEMA_GET_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetSchemaResponse', $this->instance->send($request));
    }
}