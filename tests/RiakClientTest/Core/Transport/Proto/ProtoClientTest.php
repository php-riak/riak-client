<?php

namespace RiakClientTest\Core\Transport\Proto;

use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbErrorResp;
use DrSlump\Protobuf\Protobuf;
use RiakClientTest\TestCase;

class ProtoClientTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
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
}