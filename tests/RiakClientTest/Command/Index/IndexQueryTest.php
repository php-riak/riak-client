<?php

namespace RiakClientTest\Command\Index;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Index\IntIndexQuery;
use Riak\Client\Command\Index\BinIndexQuery;

class IndexQueryTest extends TestCase
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

    public function testBuildIntIndex()
    {
        $builder = IntIndexQuery::builder($this->namespace, 'foobar', -1, -1)
            ->withNamespace($this->namespace)
            ->withIndexName('index-name')
            ->withReturnTerms(true)
            ->withMaxResults(20)
            ->withTimeout(100)
            ->withStart(0)
            ->withEnd(10);

        $command = $builder->build();
        $query   = $command->getQuery();

        $this->assertInstanceOf('Riak\Client\Core\Query\RiakIndexQuery', $query);
        $this->assertInstanceOf('Riak\Client\Command\Index\IntIndexQuery', $command);
        $this->assertEquals('index-name_int', $query->getIndexName());
        $this->assertSame($this->namespace, $query->getNamespace());
        $this->assertSame(20, $query->getMaxResults());
        $this->assertTrue($query->getReturnTerms());
        $this->assertSame(0, $query->getStart());
        $this->assertSame(10, $query->getEnd());
    }

    public function testBuildBinIndex()
    {
        $builder = BinIndexQuery::builder($this->namespace, 'foo', 'bar')
            ->withNamespace($this->namespace)
            ->withTermFilter('@gmail.com')
            ->withIndexName('index-mail')
            ->withPaginationSort(true)
            ->withMatch('val');

        $command = $builder->build();
        $query   = $command->getQuery();

        $this->assertInstanceOf('Riak\Client\Core\Query\RiakIndexQuery', $query);
        $this->assertInstanceOf('Riak\Client\Command\Index\BinIndexQuery', $command);
        $this->assertEquals('index-mail_bin', $query->getIndexName());
        $this->assertSame($this->namespace, $query->getNamespace());
        $this->assertSame('@gmail.com', $query->getTermFilter());
        $this->assertTrue($query->getPaginationSort());
        $this->assertSame('val', $query->getMatch());
    }
}