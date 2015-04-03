<?php

namespace RiakClientTest\Core\Transport\Proto;

use Riak\Client\Core\Transport\Proto\ProtoConnection;
use phpmock\phpunit\PHPMock;
use RiakClientTest\TestCase;

/**
 * @preserveGlobalState disabled
 * @backupStaticAttributes enabled
 */
class ProtoConnectionTest extends TestCase
{

    use PHPMock;

    const MOCK_NAMESPACE = 'Riak\Client\Core\Transport\Proto';

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoConnection
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new ProtoConnection('riak.local', 8087);
    }

    protected function createMockFunctions()
    {
        return [
            'fread'                => $this->getFunctionMock(self::MOCK_NAMESPACE, 'fread'),
            'fwrite'               => $this->getFunctionMock(self::MOCK_NAMESPACE, 'fwrite'),
            'is_resource'          => $this->getFunctionMock(self::MOCK_NAMESPACE, 'is_resource'),
            'stream_set_timeout'   => $this->getFunctionMock(self::MOCK_NAMESPACE, 'stream_set_timeout'),
            'stream_socket_client' => $this->getFunctionMock(self::MOCK_NAMESPACE, 'stream_socket_client'),
        ];
    }

    /**
     * @runInSeparateProcess
     */
    public function testCreateConnectionWithTimeout()
    {
        $this->instance->setTimeout(350);
        $this->assertEquals(350, $this->instance->getTimeout());

        $socket = tmpfile();
        $mocks  = $this->createMockFunctions();

        $mocks['stream_socket_client']
            ->expects($this->once())
            ->with($this->equalTo('tcp://riak.local:8087'))
            ->willReturn($socket);

        $mocks['is_resource']
            ->expects($this->once())
            ->with($this->equalTo($socket))
            ->willReturn(true);

        $mocks['stream_set_timeout']
            ->expects($this->once())
            ->with($this->equalTo($socket), $this->equalTo(350));

        $this->assertInstanceOf('Riak\Client\Core\Transport\Proto\ProtoStream', $this->instance->createStream());
    }

    public function testReceiveLengthHeader()
    {
        $code    = pack('C', 10);
        $message = 'message-body';
        $stream  = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $header  = pack("N", strlen($code . $message));

        $stream->expects($this->once())
            ->method('read')
            ->willReturn($header)
            ->with($this->equalTo(4));

       $this->assertEquals(13, $this->invokeMethod($this->instance, 'receiveLengthHeader', [$stream]));
    }

    public function testReceiveMessageCode()
    {
        $code    = pack('C', 10);
        $stream  = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);

        $stream->expects($this->once())
            ->method('read')
            ->willReturn($code)
            ->with($this->equalTo(1));

       $this->assertEquals(10, $this->invokeMethod($this->instance, 'receiveMessageCode', [$stream]));
    }

    public function testReceiveMessage()
    {
        $code    = pack('C', 10);
        $message = 'message-body';
        $stream  = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $header  = pack("N", strlen($code . $message));

        $stream->expects($this->exactly(3))
            ->method('read')
            ->will($this->returnValueMap([
                [4, $header],
                [1, $code],
                [12, $message],
            ]));

       $response     = $this->instance->receive($stream);
       $responseCode = $response[0];
       $responseBody = $response[1];

       $this->assertEquals(10, $responseCode);
       $this->assertEquals($message, $responseBody);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Fail to read response code
     */
    public function testReceiveInvalidCodeException()
    {
        $stream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);

        $stream->expects($this->once())
            ->method('read')
            ->willReturn(false)
            ->with($this->equalTo(1));

       $this->invokeMethod($this->instance, 'receiveMessageCode', [$stream]);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Short read on header, read 3, 4 bytes expected.
     */
    public function testReceiveInvalidHeaderSizeException()
    {
        $stream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);

        $stream->expects($this->once())
            ->method('read')
            ->willReturn('123')
            ->with($this->equalTo(4));

       $this->instance->receive($stream);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Fail to read socket response
     */
    public function testFailToReadMessagePart()
    {
        $stream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);

        $stream->expects($this->once())
            ->method('read')
            ->willReturn(false);

        $this->invokeMethod($this->instance, 'receiveMessageBody', [$stream, 1024]);
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Fail to read response headers
     */
    public function testReceiveInvalidHeaderException()
    {
        $stream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);

        $stream->expects($this->once())
            ->method('read')
            ->willReturn(false)
            ->with($this->equalTo(4));

       $this->instance->receive($stream);
    }

    /**
     * @runInSeparateProcess
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Fail to connect to : tcp://riak.local:8087 [ ]
     */
    public function testCreateConnectionException()
    {
        $mocks  = $this->createMockFunctions();

        $mocks['stream_socket_client']
            ->expects($this->once())
            ->with($this->equalTo('tcp://riak.local:8087'))
            ->willReturn(null);

        $mocks['is_resource']
            ->expects($this->once())
            ->willReturn(false);

        $this->instance->createStream();
    }
}