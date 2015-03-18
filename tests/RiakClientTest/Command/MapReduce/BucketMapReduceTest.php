<?php

namespace RiakClientTest\Command\MapReduce;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\KeyFilters;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Command\MapReduce\BucketMapReduce;

class BucketMapReduceTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

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

        $this->namespace = new RiakNamespace('type', 'bucket');
        $this->adapter   = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->node      = new RiakNode($this->adapter);
        $this->client    = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommand()
    {
        $filters = KeyFilters::filter()->startsWith('2005');
        $builder = BucketMapReduce::builder($this->namespace, KeyFilters::filter())
            ->withNamespace($this->namespace)
            ->withKeyFilter($filters)
            ->withLinkPhase('bucket-name', 'link')
            ->withMapPhase(new ErlangFunction('module', 'map_func1'))
            ->withReducePhase(new ErlangFunction('module', 'red_func1'));

        $command = $builder->build();
        $spec    = $command->getSpecification();

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Specification', $spec);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\BucketMapReduce', $command);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Input\BucketInput', $spec->getInput());
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\KeyFilters', $spec->getInput()->getFilters());
        $this->assertSame($this->namespace, $spec->getInput()->getNamespace());
        $this->assertSame($filters, $spec->getInput()->getFilters());
        $this->assertCount(3, $spec->getPhases());
    }
}