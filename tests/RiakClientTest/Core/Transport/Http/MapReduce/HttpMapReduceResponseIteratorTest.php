<?php

namespace RiakClientTest\Core\Transport\Http\Index;

use RiakClientTest\TestCase;
use Riak\Client\Core\Transport\Http\MapReduce\HttpMapReduceResponseIterator;

class HttpMapReduceResponseIteratorTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Http\MultipartResponseIterator
     */
    private $iterator;

    /**
     * @var \Riak\Client\Core\Transport\Http\MapReduce\HttpMapReduceResponseIterator
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->iterator = $this->getMock('Riak\Client\Core\Transport\Http\MultipartResponseIterator', [], [], '', false);
        $this->instance = new HttpMapReduceResponseIterator($this->iterator);
    }

    public function testReadNext()
    {
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->iterator->expects($this->once())
            ->method('valid')
            ->willReturn(true);

        $this->iterator->expects($this->once())
            ->method('current')
            ->willReturn($httpResponse);

        $httpResponse->expects($this->once())
            ->method('json')
            ->willReturn([
               'data'  => [123],
               'phase' => 1
            ]);

        $result = $this->invokeMethod($this->instance, 'readNext');

        $this->assertInstanceOf('Riak\Client\Core\Message\MapReduce\MapReduceEntry', $result);
        $this->assertEquals([123], $result->response);
        $this->assertEquals(1, $result->phase);
    }

    public function testReadNextReturnNullIfThereIsNoData()
    {
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->iterator->expects($this->once())
            ->method('valid')
            ->willReturn(true);

        $this->iterator->expects($this->once())
            ->method('current')
            ->willReturn($httpResponse);

        $httpResponse->expects($this->once())
            ->method('json')
            ->willReturn([]);

        $this->assertNull($this->invokeMethod($this->instance, 'readNext'));
    }
}