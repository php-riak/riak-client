<?php

namespace RiakClientTest\Core\Transport\Proto;

use RiakClientTest\TestCase;
use Riak\Client\ProtoBuf\RpbListKeysResp;

class ProtoStreamIteratorIteratorTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoStreamIterator
     */
    private $iterator;

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoStreamIteratorIterator
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->iterator = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStreamIterator', [], [], '', false);
        $this->instance = $this->getMockForAbstractClass('Riak\Client\Core\Transport\Proto\ProtoStreamIteratorIterator', [$this->iterator]);
    }

    public function testIsDone()
    {
        $message = new RpbListKeysResp();

        $this->assertFalse($this->invokeMethod($this->instance, 'isDone'));

        $message->done = false;
        $this->setPropertyValue($this->instance, 'message', $message);

        $this->assertFalse($this->invokeMethod($this->instance, 'isDone'));

        $message->done = true;
        $this->setPropertyValue($this->instance, 'message', $message);

        $this->assertTrue($this->invokeMethod($this->instance, 'isDone'));
    }

    public function testReadNextWhenIsNotDone()
    {
        if (defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM Fail to invoke readNext( WTF !!).');
        }

        $message = new RpbListKeysResp();

        $this->iterator->expects($this->once())
            ->method('current')
            ->willReturn($message);

        $this->instance->expects($this->once())
            ->method('extract')
            ->willReturn('value')
            ->with($this->equalTo($message));

        $this->assertEquals('value', $this->invokeMethod($this->instance, 'readNext'));
    }

    public function testReadNextWhenIsDone()
    {
        $message = new RpbListKeysResp();

        $message->done = true;
        $this->setPropertyValue($this->instance, 'message', $message);

        $this->assertNull($this->invokeMethod($this->instance, 'readNext'));
    }
}