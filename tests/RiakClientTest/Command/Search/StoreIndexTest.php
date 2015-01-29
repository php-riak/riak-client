<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Core\Query\Search\YokozunaIndex;
use Riak\Client\Core\Message\Search\PutIndexResponse;

class StoreIndexTest extends TestCase
{
    /**
     * @var \Riak\Client\RiakClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\RiakTransport
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->adapter = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->node    = new RiakNode($this->adapter);
        $this->client  = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommandBuilder()
    {
        $builder = StoreIndex::builder()
            ->withIndex(new YokozunaIndex('schema-name'));

        $this->assertInstanceOf('Riak\Client\Command\Search\Builder\StoreIndexBuilder', $builder);
        $this->assertInstanceOf('Riak\Client\Command\Search\StoreIndex', $builder->build());
    }

    public function testExecuteCommand()
    {
        $index    = new YokozunaIndex('index-name', 'schema-name');
        $response = new PutIndexResponse();
        $command  = StoreIndex::builder()
            ->withIndex($index)
            ->build();

        $index->setNVal(33);

        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutIndexRequest', $subject);
            $this->assertEquals('schema-name', $subject->schema);
            $this->assertEquals('index-name', $subject->name);
            $this->assertEquals(33, $subject->nVal);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->with($this->callback($callback))
            ->will($this->returnValue($response));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreIndexResponse', $this->client->execute($command));
    }
}