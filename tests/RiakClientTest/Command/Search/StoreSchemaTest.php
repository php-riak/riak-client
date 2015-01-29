<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Search\StoreSchema;
use Riak\Client\Core\Query\Search\YokozunaSchema;
use Riak\Client\Core\Message\Search\PutSchemaResponse;

class StoreSchemaTest extends TestCase
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
        $builder = StoreSchema::builder()
            ->withSchema(new YokozunaSchema('schema-name', 'schema-content'));

        $this->assertInstanceOf('Riak\Client\Command\Search\Builder\StoreSchemaBuilder', $builder);
        $this->assertInstanceOf('Riak\Client\Command\Search\StoreSchema', $builder->build());
    }

    public function testExecuteCommand()
    {
        $index    = new YokozunaSchema('schema-name', 'schema-content');
        $response = new PutSchemaResponse();
        $command  = StoreSchema::builder()
            ->withSchema($index)
            ->build();

        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\PutSchemaRequest', $subject);
            $this->assertEquals('schema-content', $subject->content);
            $this->assertEquals('schema-name', $subject->name);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->with($this->callback($callback))
            ->will($this->returnValue($response));

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreSchemaResponse', $this->client->execute($command));
    }
}