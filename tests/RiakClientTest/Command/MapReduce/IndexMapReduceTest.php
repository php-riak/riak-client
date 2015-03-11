<?php

namespace RiakClientTest\Command\MapReduce;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Command\MapReduce\IndexMapReduce;

class IndexMapReduceTest extends TestCase
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

    public function testBuildBinIndex()
    {
        $builder = IndexMapReduce::builder($this->namespace)
            ->withNamespace($this->namespace)
            ->withIndexBin('index-name')
            ->withRange('start', 'end')
            ->withMapPhase(new ErlangFunction('module', 'map_func1'))
            ->withMapPhase(new ErlangFunction('module', 'map_func2'), null, true)
            ->withLinkPhase('bucket-name', 'link')
            ->withLinkPhase('bucket-name', 'link', true)
            ->withReducePhase(new ErlangFunction('module', 'red_func1'))
            ->withReducePhase(new ErlangFunction('module', 'red_func2'), null, true);

        $command = $builder->build();
        $spec    = $command->getSpecification();

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Specification', $spec);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\IndexMapReduce', $command);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Input\IndexInput', $spec->getInput());
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Input\Index\RangeCriteria', $spec->getInput()->getCriteria());
        $this->assertEquals('start', $spec->getInput()->getCriteria()->getStart());
        $this->assertEquals('end', $spec->getInput()->getCriteria()->getEnd());
        $this->assertEquals('index-name_bin', $spec->getInput()->getIndexName());
        $this->assertSame($this->namespace, $spec->getInput()->getNamespace());
        $this->assertCount(6, $spec->getPhases());

        $phases = $spec->getPhases();

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\MapPhase', $phases[0]);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\MapPhase', $phases[1]);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\LinkPhase', $phases[2]);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\LinkPhase', $phases[3]);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\ReducePhase', $phases[4]);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\ReducePhase', $phases[5]);
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\ErlangFunction', $phases[0]->getFunction());
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\ErlangFunction', $phases[1]->getFunction());
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\ErlangFunction', $phases[4]->getFunction());
        $this->assertInstanceOf('Riak\Client\Core\Query\Func\ErlangFunction', $phases[5]->getFunction());
        $this->assertFalse($phases[0]->getKeepResult());
        $this->assertTrue($phases[1]->getKeepResult());
        $this->assertFalse($phases[2]->getKeepResult());
        $this->assertTrue($phases[3]->getKeepResult());
        $this->assertFalse($phases[4]->getKeepResult());
        $this->assertTrue($phases[5]->getKeepResult());
    }

    public function testBuildIntIndex()
    {
        $builder = IndexMapReduce::builder($this->namespace)
            ->withNamespace($this->namespace)
            ->withIndexInt('index-name')
            ->withMatchValue(10)
            ->withTimeout(100)
            ->withMapPhase(new ErlangFunction('module', 'map_func1'))
            ->withReducePhase(new ErlangFunction('module', 'red_func1'));

        $command = $builder->build();
        $spec    = $command->getSpecification();

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Specification', $spec);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\IndexMapReduce', $command);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Input\IndexInput', $spec->getInput());
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Input\Index\MatchCriteria', $spec->getInput()->getCriteria());
        $this->assertEquals('index-name_int', $spec->getInput()->getIndexName());
        $this->assertSame($this->namespace, $spec->getInput()->getNamespace());
        $this->assertEquals(10, $spec->getInput()->getCriteria()->getValue());
        $this->assertEquals(100, $spec->getTimeout());
        $this->assertCount(2, $spec->getPhases());

        $phases = $spec->getPhases();

        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\MapPhase', $phases[0]);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Phase\ReducePhase', $phases[1]);
    }
}