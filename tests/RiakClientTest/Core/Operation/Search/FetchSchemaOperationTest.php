<?php

namespace RiakClientTest\Core\Operation\Search;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Search\GetSchemaResponse;
use Riak\Client\Core\Operation\Search\FetchSchemaOperation;

class FetchSchemaOperationTest extends TestCase
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

    public function testCreateGetSchemaRequest()
    {
        $operation = new FetchSchemaOperation('index-name');
        $request   = $this->invokeMethod($operation, 'createGetSchemaRequest', []);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetSchemaRequest', $request);
        $this->assertEquals('index-name', $request->name);
    }

    public function testExecuteOperation()
    {
        $response  = new GetSchemaResponse();
        $operation = new FetchSchemaOperation('index-name');
        $callback  = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetSchemaRequest', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->willReturn($response)
            ->with($this->callback($callback));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchSchemaResponse', $operation->execute($this->adapter));
    }
}