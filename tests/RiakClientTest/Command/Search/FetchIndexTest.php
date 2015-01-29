<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Search\FetchIndex;
use Riak\Client\Core\Message\Search\GetIndexResponse;

class FetchIndexTest extends TestCase
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
        $builder = FetchIndex::builder()
            ->withIndexName('schema-name');

        $this->assertInstanceOf('Riak\Client\Command\Search\Builder\FetchIndexBuilder', $builder);
        $this->assertInstanceOf('Riak\Client\Command\Search\FetchIndex', $builder->build());
    }

    public function testExecuteCommand()
    {
        $response = new GetIndexResponse();
        $command  = FetchIndex::builder()
            ->withIndexName('index-name')
            ->build();

        $response->nVal   = 10;
        $response->name   = 'index-name';
        $response->schema = 'schema-name';

        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetIndexRequest', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->with($this->callback($callback))
            ->will($this->returnValue($response));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchIndexResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\Search\YokozunaIndex', $result->getIndex());
        $this->assertEquals('schema-name', $result->getIndex()->getSchema());
        $this->assertEquals('index-name', $result->getIndex()->getName());
        $this->assertEquals(10, $result->getIndex()->getNVal());
    }
}