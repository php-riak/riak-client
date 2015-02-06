<?php

namespace RiakClientTest\Core\Transport\Http\Index;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Http\Index\HttpIndexQueryResponseIterator;

class HttpIndexQueryIteratorTest extends TestCase
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
     * @var \Riak\Client\Core\Transport\Http\Index\HttpIndexQueryResponseIterator
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->request  = new IndexQueryRequest();
        $this->iterator = $this->getMock('Riak\Client\Core\Transport\Http\MultipartResponseIterator', [], [], '', false);
        $this->instance = new HttpIndexQueryResponseIterator($this->request, $this->iterator);
    }

    public function testIteratorFromResults()
    {
        $this->request->qtype = 'eq';
        $this->request->key   = 'index-key';

        $result = [
            ['index-key' => 'object-key1'],
            ['index-key' => 'object-key2'],
        ];

        $iterator = $this->invokeMethod($this->instance, 'iteratorFromResults', [$result]);
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
}