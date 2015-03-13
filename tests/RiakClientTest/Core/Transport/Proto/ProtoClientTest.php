<?php

namespace RiakClientTest\Core\Transport\Proto;

use Riak\Client\Core\Transport\Proto\ProtoClient;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbErrorResp;
use Riak\Client\ProtoBuf\RpbPutResp;
use Riak\Client\ProtoBuf\RpbPutReq;
use DrSlump\Protobuf\Protobuf;
use RiakClientTest\TestCase;

class ProtoClientTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoConnection
     */
    private $connection;

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->connection = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoConnection', [], [], '', false);
        $this->instance   = new ProtoClient($this->connection);
    }

    /**
     * @expectedException Riak\Client\RiakException
     * @expectedExceptionMessage Unexpected protobuf response code: 999
     */
    public function testReceiveInvalidMessageException()
    {
        $stream      = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $messageCode = 10;

        $this->connection->expects($this->once())
            ->method('receive')
            ->willReturn([999, null])
            ->with($this->equalTo($stream));

        $this->instance->receiveMessage($stream, $messageCode);
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

        throw $this->invokeMethod($this->instance, 'createResponseException', [0, Protobuf::encode($message)]);
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

    public function testSendMessage()
    {
        $message  = new RpbPutReq();
        $reqCode  = RiakMessageCodes::PUT_REQ;
        $respCode = RiakMessageCodes::PUT_RESP;
        $respBody = Protobuf::encode(new RpbPutResp());
        $stream   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);

        $this->connection->expects($this->once())
            ->method('send')
            ->willReturn($stream);

        $this->connection->expects($this->once())
            ->method('receive')
            ->willReturn([$respCode, $respBody])
            ->with($this->equalTo($stream));

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbPutResp', $this->instance->send($message, $reqCode, $respCode));
    }
}