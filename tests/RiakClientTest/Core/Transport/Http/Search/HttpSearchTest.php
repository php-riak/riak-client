<?php

namespace RiakClientTest\Core\Transport\Http\Search;

use Riak\Client\Core\Transport\Http\Search\HttpSearch;
use Riak\Client\Core\Message\Search\SearchRequest;
use RiakClientTest\TestCase;

class HttpSearchTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Search\HttpSearch
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpSearch($this->client);
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new SearchRequest();
        $url        = '/search/query/index-name';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $getRequest->start   = 1;
        $getRequest->rows    = 10;
        $getRequest->op      = 'and';
        $getRequest->sort    = 'name';
        $getRequest->df      = 'name';
        $getRequest->presort = 'score';
        $getRequest->q       = 'name:Fabio*';
        $getRequest->index   = 'index-name';
        $getRequest->filter  = 'age:[18 TO *]';
        $getRequest->fl      = ['name', 'age'];

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->exactly(10))
            ->method('add')
            ->will($this->returnValueMap([
                ['q', 'name:Fabio*', $query],
                ['presort', 'score', $query],
                ['sort', 'name', $query],
                ['start', 1, $query],
                ['rows', 10, $query],
                ['op', 'and', $query],
                ['fl', 'name,age', $query],
                ['df', 'name', $query],
                ['fq', 'age:[18 TO *]', $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    public function testSearchRequestContent()
    {
        $request      = new SearchRequest();
        $query        = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo('/search/query/index-name'))
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->with($this->equalTo($httpRequest))
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $httpResponse->expects($this->once())
            ->method('json')
            ->willReturn(['response' => [
                'numFound'  => 2,
                'maxScore'  => 1,
                'docs'      => [
                    [
                        'name'     => 'Fabio B. Silva',
                        'username' => 'fabios',
                    ],
                    [
                        'name'     => 'Fabio B. Silva',
                        'username' => 'FabioBatSilva',
                    ]
                ],
            ]]);

        $request->index = 'index-name';
        $request->q     = 'name:Fabio*';

        $result = $this->instance->send($request);

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\SearchResponse', $result);
        $this->assertEquals(2, $result->numFound);
        $this->assertEquals(1, $result->maxScore);
        $this->assertCount(2, $result->docs);
        $this->assertArrayHasKey('name', $result->docs[0]);
        $this->assertArrayHasKey('name', $result->docs[1]);
        $this->assertArrayHasKey('username', $result->docs[0]);
        $this->assertArrayHasKey('username', $result->docs[1]);
    }

    public function testDocToArray()
    {
        $doc = [
            'name'     => 'Fabio B. Silva',
            'username' => 'FabioBatSilva'
        ];

        $result = $this->invokeMethod($this->instance, 'docToArray', [$doc]);

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('username', $result);

        $this->assertEquals(['Fabio B. Silva'], $result['name']);
        $this->assertEquals(['FabioBatSilva'], $result['username']);
    }

    public function testDocToArrayMulti()
    {
        $doc = [
            'multi_ss' => ['1','2','3']
        ];

        $result = $this->invokeMethod($this->instance, 'docToArray', [$doc]);

        $this->assertArrayHasKey('multi_ss', $result);
        $this->assertEquals(['1', '2', '3'], $result['multi_ss']);
    }

    public function testEnptyDocToArray()
    {
        $this->assertEquals([], $this->invokeMethod($this->instance, 'docToArray', [[]]));
    }
}