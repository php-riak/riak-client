<?php

namespace RiakClientTest\Core\Transport\Proto;

use Riak\Client\Core\Transport\Proto\ProtoClient;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbErrorResp;
use malkusch\phpmock\phpunit\MockDelegateFunction;
use malkusch\phpmock\phpunit\MockObjectProxy;
use malkusch\phpmock\MockBuilder;
use malkusch\phpmock\Mock;
use DrSlump\Protobuf\Protobuf;
use RiakClientTest\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 * @backupStaticAttributes enabled
 */
class ProtoClientTest extends TestCase
{
    const MOCK_NAMESPACE = 'Riak\Client\Core\Transport\Proto';

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new ProtoClient('riak.local', 8087);
    }

    protected function tearDown()
    {
        parent::tearDown();
        Mock::disableAll();
    }

    /**
     * @param type $name
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

    /**
     * @expectedException Riak\Client\RiakException
     * @expectedExceptionMessage Some Riak Error
     * @expectedExceptionCode -10
     */
    public function testThrowResponseException()
    {
        $message = new RpbErrorResp();

        $message->setErrmsg('Some Riak Error');
        $message->setErrcode(-10);

        $this->invokeMethod($this->instance, 'throwResponseException', [0, Protobuf::encode($message)]);
    }

    public function testClassForCode()
    {
        $this->assertEquals('Riak\Client\ProtoBuf\DtFetchResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::DT_FETCH_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\DtUpdateResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::DT_UPDATE_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbErrorResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::ERROR_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbGetBucketResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::GET_BUCKET_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbErrorResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::ERROR_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbGetResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::GET_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbGetServerInfoResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::GET_SERVER_INFO_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbListBucketsResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::LIST_BUCKETS_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbListKeysResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::LIST_KEYS_RESP]));
        $this->assertEquals('Riak\Client\ProtoBuf\RpbPutResp', $this->invokeMethod($this->instance, 'classForCode', [RiakMessageCodes::PUT_RESP]));

        $this->assertNull($this->invokeMethod($this->instance, 'classForCode', [-100]));
    }

    public function testCreateConnectionWithTimeout()
    {
        $this->instance->setTimeout(350);

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

        $this->assertSame($socket, $this->invokeMethod($this->instance, 'getConnection'));
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Fail to read response headers
     */
    public function testReceiveInvalidHeaderException()
    {
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

        $mocks['fread']
            ->expects($this->once())
            ->with($this->equalTo($socket), $this->equalTo(4))
            ->willReturn(false);

       $this->invokeMethod($this->instance, 'receive');
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

        $this->invokeMethod($this->instance, 'getConnection');
    }
}