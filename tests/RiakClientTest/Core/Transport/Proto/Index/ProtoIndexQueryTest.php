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
        $this->assertEquals(IndexQueryType::range, $message->qtype);
        $this->assertEquals('bucket', $message->bucket);
        $this->assertEquals('type', $message->type);
        $this->assertEquals('regex', $message->term_regex);
        $this->assertEquals('index', $message->index);
        $this->assertEquals('arg1', $message->range_min);
        $this->assertEquals('arg2', $message->range_max);
        $this->assertEquals(999, $message->max_results);
        $this->assertEquals(888, $message->timeout);
        $this->assertEquals(true, $message->return_terms);
        $this->assertEquals(true, $message->pagination_sort);
        $this->assertEquals('continuation', $message->continuation);
    }

    public function testIndexQueryMessageResponse()
    {
        $request   = new IndexQueryRequest();
        $rpbStream = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $callback  = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbIndexReq', $subject);
            $this->assertEquals(IndexQueryType::range, $subject->qtype);
            $this->assertEquals('bucket', $subject->bucket);
            $this->assertEquals('type', $subject->type);
            $this->assertEquals('regex', $subject->term_regex);
            $this->assertEquals('index', $subject->index);
            $this->assertEquals('arg1', $subject->range_min);
            $this->assertEquals('arg2', $subject->range_max);
            $this->assertEquals(999, $subject->max_results);
            $this->assertEquals(true, $subject->return_terms);
            $this->assertEquals(true, $subject->pagination_sort);
            $this->assertEquals('continuation', $subject->continuation);

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