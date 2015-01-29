<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Search\FetchSchema;
use Riak\Client\Core\Message\Search\GetSchemaResponse;

class FetchSchemaTest extends TestCase
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
        $builder = FetchSchema::builder()
            ->withSchemaName('schema-name');

        $this->assertInstanceOf('Riak\Client\Command\Search\Builder\FetchSchemaBuilder', $builder);
        $this->assertInstanceOf('Riak\Client\Command\Search\FetchSchema', $builder->build());
    }

    public function testExecuteCommand()
    {
        $response = new GetSchemaResponse();
        $command  = FetchSchema::builder()
            ->withSchemaName('schema-name')
            ->build();

        $response->name    = 'schema-name';
        $response->content = 'schema-content';

        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\GetSchemaRequest', $subject);
            $this->assertEquals('schema-name', $subject->name);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->with($this->callback($callback))
            ->will($this->returnValue($response));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchSchemaResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\Search\YokozunaSchema', $result->getSchema());
        $this->assertEquals('schema-content', $result->getSchema()->getContent());
        $this->assertEquals('schema-name', $result->getSchema()->getName());
    }
}