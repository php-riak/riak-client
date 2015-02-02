<?php

namespace RiakClientTest\Core\Transport\Proto;

use Riak\Client\Core\Transport\Proto\ProtoConnection;
use malkusch\phpmock\phpunit\MockDelegateFunction;
use malkusch\phpmock\phpunit\MockObjectProxy;
use malkusch\phpmock\MockBuilder;
use malkusch\phpmock\Mock;
use RiakClientTest\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * @backupStaticAttributes enabled
 */
class ProtoConnectionTest extends TestCase
{
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

    protected function tearDown()
    {
        parent::tearDown();
        Mock::disableAll();
    }

    /**
     * @param string $name
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getFunctionMock($name)
    {
        $mock = $this->getMockBuilder('malkusch\phpmock\phpunit\MockDelegate')->getMock();

        $functionMockBuilder = new MockBuilder();
        $functionMockBuilder->setNamespace(self::MOCK_NAMESPACE)
            ->setFunctionProvider(new MockDelegateFunction($mock))
            ->setName($name);

        $functionMock = $functionMockBuilder->build();
        $functionMock->enable();

        return new MockObjectProxy($mock);
    }

    protected function createMockFunctions()
    {
        return [
            'fread'                => $this->getFunctionMock('fread'),
            'fwrite'               => $this->getFunctionMock('fwrite'),
            'is_resource'          => $this->getFunctionMock('is_resource'),
            'stream_set_timeout'   => $this->getFunctionMock('stream_set_timeout'),
            'stream_socket_client' => $this->getFunctionMock('stream_socket_client'),
        ];
    }

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

        $this->assertInstanceOf('GuzzleHttp\Stream\Stream', $this->instance->createStream());
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Fail to read response headers
     */
    public function testReceiveInvalidHeaderException()
    {
        $stream = $this->getMock('GuzzleHttp\Stream\Stream', [], [], '', false);

        $stream->expects($this->once())
            ->method('read')
            ->willReturn(false)
            ->with($this->equalTo(4));

       $this->instance->receive($stream);
    }

    /**
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