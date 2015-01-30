<?php

namespace RiakClientTest\Core\Transport\Proto\DataType;

use Riak\Client\Core\Transport\Proto\DataType\ProtoPut;
use Riak\Client\Core\Message\DataType\PutRequest;
use Riak\Client\ProtoBuf\MapField\MapFieldType;
use Riak\Client\Core\Query\Crdt\Op\RegisterOp;
use Riak\Client\Core\Query\Crdt\Op\CounterOp;
use Riak\Client\Core\Query\Crdt\Op\MapOp;
use Riak\Client\Core\Query\Crdt\Op\SetOp;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\DtUpdateResp;
use Riak\Client\ProtoBuf\MapEntry;
use Riak\Client\ProtoBuf\MapField;
use RiakClientTest\TestCase;

class ProtoPutTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\DataType\ProtoPut
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
        $request = new PutRequest();

        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $request->w          = 3;
        $request->pw         = 2;
        $request->dw         = 1;
        $request->returnBody = true;
        $request->op         = new CounterOp(10);

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\DtUpdateReq', $message);

        $this->assertEquals(3, $message->w);
        $this->assertEquals(2, $message->pw);
        $this->assertEquals(1, $message->dw);
        $this->assertTrue($message->return_body);
        $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $message->op);
    }

    public function testPutMessageResponse()
    {
        $request  = new PutRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtUpdateReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->op);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $request->op     = new CounterOp(10);
        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $this->client->expects($this->once())
            ->method('send')
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_REQ),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\PutResponse', $this->instance->send($request));
    }

    public function testPutMessageResponseCounterValue()
    {
        $rpbResp  = new DtUpdateResp();
        $request  = new PutRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtUpdateReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->op);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $request->op     = new CounterOp(10);
        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $rpbResp->setCounterValue(10);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_REQ),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_RESP)
            );

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\PutResponse', $response);
        $this->assertEquals('counter', $response->type);
        $this->assertEquals(10, $response->value);
    }

    public function testPutMessageResponseSetValue()
    {
        $request  = new PutRequest();
        $rpbResp  = new DtUpdateResp();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtUpdateReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->op);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $request->op     = new SetOp([1,2,3], []);
        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $rpbResp->setSetValue([1,2,3]);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_REQ),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_RESP)
            );

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\PutResponse', $response);
        $this->assertEquals([1,2,3], $response->value);
        $this->assertEquals('set', $response->type);
    }

    public function testPutMessageResponseMapValue()
    {
        $request  = new PutRequest();
        $rpbResp  = new DtUpdateResp();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtUpdateReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->op);
            $this->assertEquals('test_bucket', $subject->bucket);
            $this->assertEquals('default', $subject->type);
            $this->assertEquals('1', $subject->key);

            return true;
        };

        $updates = [
            'register'  => [
                'registerField' => new RegisterOp('Register Val')
            ],
        ];

        $request->op     = new MapOp($updates, []);
        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $mapEntryValue[0] = new MapEntry();
        $mapEntryValue[0]->setField(new MapField());
        $mapEntryValue[0]->field->setName('registerField');
        $mapEntryValue[0]->field->setType(MapFieldType::REGISTER);
        $mapEntryValue[0]->setRegisterValue('Register Val');

        $rpbResp->setMapValue($mapEntryValue);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_REQ),
                $this->equalTo(RiakMessageCodes::DT_UPDATE_RESP)
            );

        $response = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\DataType\PutResponse', $response);
        $this->assertEquals(['registerField' => 'Register Val'], $response->value);
        $this->assertEquals('map', $response->type);
    }
}