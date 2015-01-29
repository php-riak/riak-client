<?php

namespace RiakClientTest\Core\Operation\Search;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Core\Message\Search\PutIndexResponse;
use Riak\Client\Core\Operation\Search\StoreIndexOperation;

class StoreIndexOperationTest extends TestCase
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

    public function testCreatePutIndexRequest()
    {
        $index     = new YokozunaIndex('index-name', 'schema-name');
        $operation = new StoreIndexOperation($index);
        $request   = $this->invokeMethod($operation, 'createPutIndexRequest', []);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutIndexRequest', $request);
        $this->assertEquals('schema-name', $request->schema);
        $this->assertEquals('index-name', $request->name);
    }

    public function testExecuteOperation()
    {
        $response  = new PutIndexResponse();
        $index     = new YokozunaIndex(null, null);
        $operation = new StoreIndexOperation($index);
        $callback  = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutIndexRequest', $subject);
            $this->assertEquals('schema-name', $subject->schema);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $index->setName('index-name');
        $index->setSchema('schema-name');

        $this->adapter->expects($this->once())
            ->method('send')
            ->willReturn($response)
            ->with($this->callback($callback));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreIndexResponse', $operation->execute($this->adapter));
    }
}