<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Search\ProtoPutSchema;
use Riak\Client\Core\Message\Search\PutSchemaRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbPutResp;
use RiakClientTest\TestCase;

class ProtoPutSchemaTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Search\ProtoPutSchema
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoPutSchema($this->client);
    }

    public function testCreateRpbMessage()
    {
        $request = new PutSchemaRequest();

        $request->name    = 'schema-name';
        $request->content = 'schema-content';

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaSchemaPutReq', $message);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaSchema', $message->schema);
        $this->assertEquals('schema-content', $message->schema->content);
        $this->assertEquals('schema-name', $message->schema->name);
    }

    public function testPutMessageResponse()
    {
        $rpbResp  = new RpbPutResp();
        $request  = new PutSchemaRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaSchemaPutReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaSchema', $subject->schema);
            $this->assertEquals('schema-content', $subject->schema->content);
            $this->assertEquals('schema-name', $subject->schema->name);

            return true;
        };

        $request->name    = 'schema-name';
        $request->content = 'schema-content';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_SCHEMA_PUT_REQ),
                $this->equalTo(RiakMessageCodes::PUT_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutSchemaResponse', $this->instance->send($request));
    }
}