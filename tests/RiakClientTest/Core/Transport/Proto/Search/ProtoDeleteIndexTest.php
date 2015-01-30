<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Search\ProtoDeleteIndex;
use Riak\Client\Core\Message\Search\DeleteIndexResponse;
use Riak\Client\Core\Message\Search\DeleteIndexRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use RiakClientTest\TestCase;

class ProtoDeleteIndexTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Search\ProtoDeleteIndex
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoDeleteIndex($this->client);
    }

    public function testCreateRpbMessage()
    {
        $getRequest = new DeleteIndexRequest();

        $getRequest->name = 'index-name';

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexDeleteReq', $message);
        $this->assertEquals('index-name', $message->name);
    }

    public function testDeleteMessageResponse()
    {
        $response = new DeleteIndexResponse();
        $request  = new DeleteIndexRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbYokozunaIndexDeleteReq', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $request->name = 'index-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($response)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::YOKOZUNA_INDEX_DELETE_REQ),
                $this->equalTo(RiakMessageCodes::DEL_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\DeleteIndexResponse', $this->instance->send($request));
    }
}