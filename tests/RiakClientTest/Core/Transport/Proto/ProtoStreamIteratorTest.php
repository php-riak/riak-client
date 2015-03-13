<?php

namespace RiakClientTest\Core\Transport\Proto;

use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use RiakClientTest\TestCase;

class ProtoStreamIteratorTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoStreamIterator
     */
    private $instance;

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    protected $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoStream
     */
    protected $stream;

    /**
     * @var integer
     */
    protected $messageCode;

    protected function setUp()
    {
        parent::setUp();

        $this->messageCode = RiakMessageCodes::LIST_KEYS_RESP;
        $this->client      = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->stream      = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $this->instance    = new ProtoStreamIterator($this->client, $this->stream, $this->messageCode);
    }

    public function testReadNext()
    {
        $message = $this->getMock('Riak\Client\ProtoBuf\RpbListKeysResp', [], [], '', false);

        $this->client->expects($this->once())
            ->method('receiveMessage')
            ->willReturn($message)
            ->with(
                $this->equalTo($this->stream),
                $this->equalTo($this->messageCode)
            );

        $this->assertSame($message, $this->invokeMethod($this->instance, 'readNext'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage A streaming iterator cannot be rewinded.
     */
    public function testRewindException()
    {
        $message = $this->getMock('Riak\Client\ProtoBuf\RpbListKeysResp', [], [], '', false);

        $this->client->expects($this->once())
            ->method('receiveMessage')
            ->willReturn($message)
            ->with(
                $this->equalTo($this->stream),
                $this->equalTo($this->messageCode)
            );

        $this->instance->rewind();
        $this->instance->rewind();
    }
}