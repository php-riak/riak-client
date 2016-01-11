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

        $this->assertEquals(3, $message->getW());
        $this->assertEquals(2, $message->getPw());
        $this->assertEquals(1, $message->getDw());
        $this->assertTrue($message->getReturnBody());
        $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $message->getOp());
    }

    public function testPutMessageResponse()
    {
        $request  = new PutRequest();
        $callback = function($subject) {

            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtUpdateReq', $subject);
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->getOp());
            $this->assertEquals('test_bucket', $subject->getBucket());
            $this->assertEquals('default', $subject->getType());
            $this->assertEquals('1', $subject->getKey());

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
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->getOp());
            $this->assertEquals('test_bucket', $subject->getBucket());
            $this->assertEquals('default', $subject->getType());
            $this->assertEquals('1', $subject->getKey());

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
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->getOp());
            $this->assertEquals('test_bucket', $subject->getBucket());
            $this->assertEquals('default', $subject->getType());
            $this->assertEquals('1', $subject->getKey());

            return true;
        };

        $request->op     = new SetOp([1,2,3], []);
        $request->bucket = 'test_bucket';
        $request->type   = 'default';
        $request->key    = '1';

        $rpbResp->addSetValue('1');
        $rpbResp->addSetValue('2');
        $rpbResp->addSetValue('3');

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
            $this->assertInstanceOf('Riak\Client\ProtoBuf\DtOp', $subject->getOp());
            $this->assertEquals('test_bucket', $subject->getBucket());
            $this->assertEquals('default', $subject->getType());
            $this->assertEquals('1', $subject->getKey());

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

        $mapEntryValue = new MapEntry();
        $mapEntryValue->setField(new MapField());
        $mapEntryValue->getField()->setName('registerField');
        $mapEntryValue->getField()->setType(MapFieldType::REGISTER());
        $mapEntryValue->setRegisterValue('Register Val');

        $rpbResp->addMapValue($mapEntryValue);

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