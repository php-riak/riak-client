<?php

namespace RiakClientTest\Core\Operation\Search;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Search\DeleteIndexResponse;
use Riak\Client\Core\Operation\Search\DeleteIndexOperation;

class DeleteIndexOperationTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\RiakTransport
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $this->adapter = $this->getMock('Riak\Client\Core\RiakTransport');
    }

    public function testCreateDeleteIndexRequest()
    {
        $operation = new DeleteIndexOperation('index-name');
        $request   = $this->invokeMethod($operation, 'createDeleteIndexRequest', []);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\DeleteIndexRequest', $request);
        $this->assertEquals('index-name', $request->name);
    }

    public function testExecuteOperation()
    {
        $response  = new DeleteIndexResponse();
        $operation = new DeleteIndexOperation('index-name');
        $callback  = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\DeleteIndexRequest', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->willReturn($response)
            ->with($this->callback($callback));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\DeleteIndexResponse', $operation->execute($this->adapter));
    }
}