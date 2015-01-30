<?php

namespace RiakClientTest\Core\Transport\Proto\DataType;

use Riak\Client\Core\Transport\Proto\DataType\ProtoGet;
use Riak\Client\Core\Message\DataType\GetRequest;
use Riak\Client\ProtoBuf\MapField\MapFieldType;
use Riak\Client\ProtoBuf\DtFetchResp\DataType;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\DtFetchResp;
use Riak\Client\ProtoBuf\MapEntry;
use Riak\Client\ProtoBuf\MapField;
use Riak\Client\ProtoBuf\DtValue;
use RiakClientTest\TestCase;

class ProtoGetTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\DataType\ProtoGet
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
        $getRequest->key    = '1';

        $getRequest->r           = 3;
        $getRequest->pr          = 2;
        $getRequest->basicQuorum = true;
        $getRequest->notfoundOk  = true;

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$getRequest]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\DtFetchReq', $message);
        $this->assertEquals(3, $message->r);
        $this->assertEquals(2, $message->pr);
        $this->assertTrue($message->notfound_ok);
        $this->assertTrue($message->basic_quorum);
    }

    public function testGetMessageResponse()
    {
        $request  = new GetRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtFetchReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->callback($callback), $this->equalTo(RiakMessageCodes::DT_FETCH_REQ), $this->equalTo(RiakMessageCodes::DT_FETCH_RESP));

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\GetResponse', $this->instance->send($request));
    }

    public function testGetMessageResponseCounterValue()
    {
        $rpbDtVal = new DtValue();
        $rpbResp  = new DtFetchResp();
        $request  = new GetRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtFetchReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $rpbDtVal->setCounterValue(10);
        $rpbResp->setValue($rpbDtVal);
        $rpbResp->setType(DataType::COUNTER);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::DT_FETCH_REQ),
                $this->equalTo(RiakMessageCodes::DT_FETCH_RESP)
            );

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\GetResponse', $response);
        $this->assertEquals('counter', $response->type);
        $this->assertEquals(10, $response->value);
    }

    public function testGetMessageResponseSetValue()
    {
        $rpbDtVal = new DtValue();
        $rpbResp  = new DtFetchResp();
        $request  = new GetRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtFetchReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $rpbDtVal->setSetValue([1,2,3]);
        $rpbResp->setValue($rpbDtVal);
        $rpbResp->setType(DataType::SET);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::DT_FETCH_REQ),
                $this->equalTo(RiakMessageCodes::DT_FETCH_RESP)
            );

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\GetResponse', $response);
        $this->assertEquals([1,2,3], $response->value);
        $this->assertEquals('set', $response->type);
    }

    public function testGetMessageResponseMapValue()
    {
        $rpbDtVal = new DtValue();
        $rpbResp  = new DtFetchResp();
        $request  = new GetRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtFetchReq', $subject);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $mapEntryValue[0] = new MapEntry();
        $mapEntryValue[0]->setField(new MapField());
        $mapEntryValue[0]->field->setName('registerField');
        $mapEntryValue[0]->field->setType(MapFieldType::REGISTER);
        $mapEntryValue[0]->setRegisterValue('Register Val');

        $rpbDtVal->setMapValue($mapEntryValue);
        $rpbResp->setValue($rpbDtVal);
        $rpbResp->setType(DataType::MAP);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::DT_FETCH_REQ),
                $this->equalTo(RiakMessageCodes::DT_FETCH_RESP)
            );

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\GetResponse', $response);
        $this->assertEquals(['registerField' => 'Register Val'], $response->value);
        $this->assertEquals('map', $response->type);
    }
}