<?php

namespace RiakClientTest\Core\Transport\Proto\MapReduce;

use Riak\Client\Core\Transport\Proto\MapReduce\ProtoMapReduce;
use Riak\Client\Core\Message\MapReduce\MapReduceRequest;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use RiakClientTest\TestCase;

class ProtoMapReduceTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Index\ProtoIndexQuery
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoMapReduce($this->client);
    }

    public function testCreateRpbMessage()
    {
        $mapred  = '{"inputs":"test", "query":[{"link":{"bucket":"test"}},{"map":{"language":"javascript","name":"Riak.mapValuesJson"}}]}';
        $request = new MapReduceRequest();

        $request->request = $mapred;

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbMapRedReq', $message);
        $this->assertEquals($mapred, $message->request);
    }

    public function testMapRedMessageResponse()
    {
        $request   = new MapReduceRequest();
        $mapred    = '{"inputs":"test", "query":[{"link":{"bucket":"test"}},{"map":{"language":"javascript","name":"Riak.mapValuesJson"}}]}';
        $rpbStream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $callback  = function($subject) use ($mapred) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbMapRedReq', $subject);
            $this->assertEquals($mapred, $subject->request);

            return true;
        };

        $request->request = $mapred;

        $this->client->expects($this->once())
            ->method('emit')
            ->willReturn($rpbStream)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::MAPRED_REQ)
            );

        $result = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\MapReduce\MapReduceResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Proto\MapReduce\ProtoMapReduceResponseIterator', $result->iterator);
    }
}