<?php

namespace RiakClientTest\Core\Transport\Proto\Index;

use RiakClientTest\TestCase;
use Riak\Client\ProtoBuf\RpbPair;
use Riak\Client\ProtoBuf\RpbIndexResp;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;
use Riak\Client\Core\Transport\Proto\Index\ProtoIndexQueryResponseIterator;

class ProtoIndexQueryIteratorTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     */
    private $request;

    /**
     * @var \Riak\Client\Core\Transport\Http\MultipartResponseIterator
     */
    private $iterator;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Index\ProtoIndexQueryResponseIterator
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->request  = new IndexQueryRequest();
        $this->iterator = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStreamIterator', [], [], '', false);
        $this->instance = new ProtoIndexQueryResponseIterator($this->request, $this->iterator);
    }

    public function testIteratorFromResults()
    {
        $this->request->qtype = 'eq';
        $this->request->key   = 'index-key';

        $pairs[]  = new RpbPair();
        $pairs[]  = new RpbPair();

        $pairs[0]->key   = 'index-key';
        $pairs[1]->key   = 'index-key';
        $pairs[0]->value = 'object-key1';
        $pairs[1]->value = 'object-key2';

        $iterator = $this->invokeMethod($this->instance, 'iteratorFromResults', [$pairs]);
        $values   = iterator_to_array($iterator);

        $this->assertCount(2, $values);
        $this->assertInstanceOf('Iterator', $iterator);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[0]);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[1]);

        $this->assertEquals('object-key1', $values[0]->objectKey);
        $this->assertEquals('object-key2', $values[1]->objectKey);
        $this->assertEquals('index-key', $values[0]->indexKey);
        $this->assertEquals('index-key', $values[0]->indexKey);
    }

    public function testIteratorFromKeys()
    {
        $this->request->qtype    = 'range';
        $this->request->rangeMin = 1;
        $this->request->rangeMax = 4;

        $iterator = $this->invokeMethod($this->instance, 'iteratorFromKeys', [[1,2,3]]);
        $values   = iterator_to_array($iterator);

        $this->assertCount(3, $values);
        $this->assertInstanceOf('Iterator', $iterator);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[0]);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[1]);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[2]);

        $this->assertEquals(1, $values[0]->objectKey);
        $this->assertEquals(2, $values[1]->objectKey);
        $this->assertEquals(3, $values[2]->objectKey);
        $this->assertNull($values[0]->indexKey);
        $this->assertNull($values[0]->indexKey);
        $this->assertNull($values[0]->indexKey);
    }

    public function testIteratorFromKeysMatch()
    {
        $this->request->qtype = 'eq';
        $this->request->key   = 3;

        $iterator = $this->invokeMethod($this->instance, 'iteratorFromKeys', [[1,2,3]]);
        $values   = iterator_to_array($iterator);

        $this->assertCount(3, $values);
        $this->assertInstanceOf('Iterator', $iterator);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[0]);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[1]);
        $this->assertInstanceOf('Riak\Client\Core\Message\Index\IndexEntry', $values[2]);

        $this->assertEquals(1, $values[0]->objectKey);
        $this->assertEquals(2, $values[1]->objectKey);
        $this->assertEquals(3, $values[2]->objectKey);
        $this->assertEquals(3, $values[0]->indexKey);
        $this->assertEquals(3, $values[0]->indexKey);
        $this->assertEquals(3, $values[0]->indexKey);
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage A streaming iterator cannot be rewind
     */
    public function testInvalidIteratorElementException()
    {
        $stream   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoStream', [], [], '', false);
        $client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $iterator = new ProtoStreamIterator($client, $stream, RiakMessageCodes::INDEX_RESP);
        $instance = new ProtoIndexQueryResponseIterator($this->request, $iterator);
        $rpbResp  = new RpbIndexResp();

        $client->expects($this->once())
            ->method('receiveMessage')
            ->willReturn($rpbResp)
            ->with($this->equalTo($stream), $this->equalTo(RiakMessageCodes::INDEX_RESP));

        $instance->rewind();
        $instance->current();
        $instance->rewind();
    }
}