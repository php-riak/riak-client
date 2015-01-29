<?php

namespace RiakClientTest\Core\Operation\Search;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Search\GetIndexResponse;
use Riak\Client\Core\Operation\Search\FetchIndexOperation;

class FetchIndexOperationTest extends TestCase
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

    public function testCreateGetIndexRequest()
    {
        $operation = new FetchIndexOperation('index-name');
        $request   = $this->invokeMethod($operation, 'createGetIndexRequest', []);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetIndexRequest', $request);
        $this->assertEquals('index-name', $request->name);
    }

    public function testExecuteOperation()
    {
        $response  = new GetIndexResponse();
        $operation = new FetchIndexOperation('index-name');
        $callback  = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetIndexRequest', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->willReturn($response)
            ->with($this->callback($callback));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchIndexResponse', $operation->execute($this->adapter));
    }
}