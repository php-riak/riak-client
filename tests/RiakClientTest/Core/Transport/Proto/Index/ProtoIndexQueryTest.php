<?php

namespace RiakClientTest\Core\Transport\Proto\Index;

use Riak\Client\Core\Transport\Proto\Index\ProtoIndexQuery;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\ProtoBuf\RpbIndexReq\IndexQueryType;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbPair;
use RiakClientTest\TestCase;

class ProtoIndexQueryTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Index\ProtoIndexQuery
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoIndexQuery($this->client);
    }

    /**
     * @param string $key
     * @param string $val
     *
     * @return \Riak\Client\ProtoBuf\RpbPair
     */
    public function createRpbPair($key, $val)
    {
        $pair = new RpbPair();

        $pair->key   = $key;
        $pair->value = $val;

        return $pair;
    }

    public function testCreateRpbMessage()
    {
        $request = new IndexQueryRequest();

        $request->qtype          = 'range';
        $request->rangeMin       = 'arg1';
        $request->rangeMax       = 'arg2';
        $request->type           = 'type';
        $request->index          = 'index';
        $request->bucket         = 'bucket';
        $request->termRegex      = 'regex';
        $request->continuation   = 'continuation';
        $request->maxResults     = 999;
        $request->timeout        = 888;
        $request->returnTerms    = true;
        $request->paginationSort = true;

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbIndexReq', $message);
        $this->assertEquals(IndexQueryType::range(), $message->getQtype());
        $this->assertEquals('bucket', $message->getBucket());
        $this->assertEquals('type', $message->getType());
        $this->assertEquals('regex', $message->getTermRegex());
        $this->assertEquals('index', $message->getIndex());
        $this->assertEquals('arg1', $message->getRangeMin());
        $this->assertEquals('arg2', $message->getRangeMax());
        $this->assertEquals(999, $message->getMaxResults());
        $this->assertEquals(888, $message->getTimeout());
        $this->assertEquals(true, $message->getReturnTerms());
        $this->assertEquals(true, $message->getPaginationSort());
        $this->assertEquals('continuation', $message->getContinuation());
    }

    public function testIndexQueryMessageResponse()
    {
        $request   = new IndexQueryRequest();
        $rpbStream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $callback  = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbIndexReq', $subject);
            $this->assertEquals(IndexQueryType::range(), $subject->getQtype());
            $this->assertEquals('bucket', $subject->getBucket());
            $this->assertEquals('type', $subject->getType());
            $this->assertEquals('regex', $subject->getTermRegex());
            $this->assertEquals('index', $subject->getIndex());
            $this->assertEquals('arg1', $subject->getRangeMin());
            $this->assertEquals('arg2', $subject->getRangeMax());
            $this->assertEquals(999, $subject->getMaxResults());
            $this->assertEquals(true, $subject->getReturnTerms());
            $this->assertEquals(true, $subject->getPaginationSort());
            $this->assertEquals('continuation', $subject->getContinuation());

            return true;
        };

        $request->qtype          = 'range';
        $request->rangeMin       = 'arg1';
        $request->rangeMax       = 'arg2';
        $request->type           = 'type';
        $request->index          = 'index';
        $request->bucket         = 'bucket';
        $request->termRegex      = 'regex';
        $request->continuation   = 'continuation';
        $request->maxResults     = 999;
        $request->returnTerms    = true;
        $request->paginationSort = true;

        $this->client->expects($this->once())
            ->method('emit')
            ->willReturn($rpbStream)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::INDEX_REQ)
            );

        $result = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexQueryResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Transport\Proto\Index\ProtoIndexQueryResponseIterator', $result->iterator);
    }
}