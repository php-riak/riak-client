<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Search\Search;
use Riak\Client\Core\Query\RiakSearchQuery;
use Riak\Client\Core\Message\Search\SearchResponse;

class SearchTest extends TestCase
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
        $builder = Search::builder()
            ->withIndex('index-name')
            ->withQuery('*:*');

        $this->assertInstanceOf('Riak\Client\Command\Search\Builder\SearchBuilder', $builder);
        $this->assertInstanceOf('Riak\Client\Command\Search\Search', $builder->build());
    }

    public function testRiakSearchQuery()
    {
        $query  = new RiakSearchQuery();
        $comamd = new Search($query);

        $this->assertSame($query, $comamd->getSearchQuery());
    }

    public function testExecuteCommand()
    {
        $response = new SearchResponse();
        $command  = Search::builder()
            ->withReturnFields(['email','name','age'])
            ->withFilterQuery('age:[18 TO *]')
            ->withDefaultOperation('and')
            ->withReturnField('username')
            ->withQuery('name:Fabio*')
            ->withDefaultField('name')
            ->withIndex('index-name')
            ->withSortField('name')
            ->withPresort('score')
            ->withNumRows(10)
            ->withStart(1)
            ->build();

        $response->maxScore = 1;
        $response->numFound = 2;
        $response->docs     = [
            [
                'email'     => ['fabio.bat.silva@gmail.com'],
                'name'      => ['Fabio B. Silva'],
                'username'  => ['FabioBatSilva'],
                'age'       => ['30'],
            ],
            [
                'email'     => ['fabio.bat.silva@gmail.com'],
                'name'      => ['Fabio B. Silva'],
                'username'  => ['fabios'],
                'age'       => ['30'],
            ]
        ];

        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Search\SearchRequest', $subject);
            $this->assertEquals('age:[18 TO *]', $subject->filter);
            $this->assertEquals('index-name', $subject->index);
            $this->assertEquals('name:Fabio*', $subject->q);
            $this->assertEquals('score', $subject->presort);
            $this->assertEquals('name', $subject->sort);
            $this->assertEquals('name', $subject->df);
            $this->assertEquals('and', $subject->op);
            $this->assertEquals(10, $subject->rows);
            $this->assertEquals(1, $subject->start);

            $this->assertCount(4, $subject->fl);
            $this->assertContains('age', $subject->fl);
            $this->assertContains('name', $subject->fl);
            $this->assertContains('email', $subject->fl);
            $this->assertContains('username', $subject->fl);

            return true;
        };

        $this->adapter->expects($this->once())
            ->method('send')
            ->with($this->callback($callback))
            ->will($this->returnValue($response));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\SearchResponse', $result);
        $this->assertEquals($response->numFound, $result->getNumResults());
        $this->assertEquals($response->maxScore, $result->getMaxScore());
        $this->assertEquals($response->docs, $result->getAllResults());
        $this->assertEquals([
            [
                'email'     => 'fabio.bat.silva@gmail.com',
                'name'      => 'Fabio B. Silva',
                'username'  => 'FabioBatSilva',
                'age'       => '30',
            ],
            [
                'email'     => 'fabio.bat.silva@gmail.com',
                'name'      => 'Fabio B. Silva',
                'username'  => 'fabios',
                'age'       => '30',
            ]
        ], $result->getSingleResults());
    }
}