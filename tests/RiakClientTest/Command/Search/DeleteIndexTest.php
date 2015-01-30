<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Search\DeleteIndex;
use Riak\Client\Core\Message\Search\DeleteIndexResponse;

class DeleteIndexTest extends TestCase
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
        $builder = DeleteIndex::builder()
            ->withIndexName('schema-name');

        $this->assertInstanceOf('Riak\Client\Command\Search\Builder\DeleteIndexBuilder', $builder);
        $this->assertInstanceOf('Riak\Client\Command\Search\DeleteIndex', $builder->build());
    }

    public function testExecuteCommand()
    {
        $response = new DeleteIndexResponse();
        $command  = DeleteIndex::builder()
            ->withIndexName('index-name')
            ->build();

        $response->nVal   = 10;
        $response->name   = 'index-name';
        $response->schema = 'schema-name';

        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\DeleteIndexRequest', $subject);
            $this->assertEquals('index-name', $subject->name);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->with($this->callback($callback))
            ->will($this->returnValue($response));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\DeleteIndexResponse', $this->client->execute($command));
    }
}