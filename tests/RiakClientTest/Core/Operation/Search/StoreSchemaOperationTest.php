<?php

namespace RiakClientTest\Core\Operation\Search;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\Search\YokozunaSchema;
use Riak\Client\Core\Message\Search\PutSchemaResponse;
use Riak\Client\Core\Operation\Search\StoreSchemaOperation;

class StoreSchemaOperationTest extends TestCase
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

    public function testCreatePutSchemaRequest()
    {
        $index     = new YokozunaSchema('schema-name', 'schema-content');
        $operation = new StoreSchemaOperation($index);
        $request   = $this->invokeMethod($operation, 'createPutSchemaRequest', []);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutSchemaRequest', $request);
        $this->assertEquals('schema-content', $request->content);
        $this->assertEquals('schema-name', $request->name);
    }

    public function testExecuteOperation()
    {
        $response  = new PutSchemaResponse();
        $index     = new YokozunaSchema(null, null);
        $operation = new StoreSchemaOperation($index);
        $callback  = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutSchemaRequest', $subject);
            $this->assertEquals('schema-content', $subject->content);
            $this->assertEquals('schema-name', $subject->name);

            return true;
        };

        $index->setName('schema-name');
        $index->setContent('schema-content');

        $this->adapter->expects($this->once())
            ->method('send')
            ->willReturn($response)
            ->with($this->callback($callback));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreSchemaResponse', $operation->execute($this->adapter));
    }
}