<?php

namespace RiakClientTest\Core\Transport\Http\Index;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Http\Index\HttpIndexQuery;

class HttpIndexQueryTest extends TestCase
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Http\Bucket\BaseHttpStrategy
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('GuzzleHttp\ClientInterface');
        $this->instance = new HttpIndexQuery($this->client);
    }

    public function testBuildPath()
    {
        $this->assertEquals('/types/type/buckets/bucket/index/index/arg1/arg2', $this->invokeMethod($this->instance, 'buildPath', ['type', 'bucket', 'index', ['arg1','arg2']]));
        $this->assertEquals('/types/type/buckets/bucket/index/index/', $this->invokeMethod($this->instance, 'buildPath', ['type', 'bucket', 'index', []]));
        $this->assertEquals('/buckets/bucket/index/key/arg1', $this->invokeMethod($this->instance, 'buildPath', [null, 'bucket', 'key', ['arg1']]));
    }

    public function testCreateRequestArgs()
    {
        $req1 = new IndexQueryRequest();
        $req2 = new IndexQueryRequest();

        $req1->qtype = 'eq';
        $req1->key   = 'arg1';

        $req2->qtype    = 'range';
        $req2->rangeMin = 'arg1';
        $req2->rangeMax = 'arg2';

        $this->assertEquals(['arg1'], $this->invokeMethod($this->instance, 'createRequestArgs', [$req1]));
        $this->assertEquals(['arg1', 'arg2'], $this->invokeMethod($this->instance, 'createRequestArgs', [$req2]));
    }

    public function testCreateRequest()
    {
        $indexReq = new IndexQueryRequest();
        $httpReq  = $this->getMock('GuzzleHttp\Message\RequestInterface');

        $indexReq->qtype    = 'range';
        $indexReq->rangeMin = 'arg1';
        $indexReq->rangeMax = 'arg2';
        $indexReq->type     = 'type';
        $indexReq->index    = 'index';
        $indexReq->bucket   = 'bucket';

        $this->client->expects($this->once())
            ->method('createRequest')
            ->willReturn($httpReq)
            ->with($this->equalTo('GET'), $this->equalTo('/types/type/buckets/bucket/index/index/arg1/arg2'));

        $this->assertSame($httpReq, $this->invokeMethod($this->instance, 'createRequest', [$indexReq]));
    }

    public function testCreateHttpRequest()
    {
        $getRequest = new IndexQueryRequest();
        $url        = '/types/type/buckets/bucket/index/index/arg1/arg2';
        $request    = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $query      = $this->getMock('GuzzleHttp\Query');

        $getRequest->qtype          = 'range';
        $getRequest->rangeMin       = 'arg1';
        $getRequest->rangeMax       = 'arg2';
        $getRequest->type           = 'type';
        $getRequest->index          = 'index';
        $getRequest->bucket         = 'bucket';
        $getRequest->termRegex      = 'regex';
        $getRequest->continuation   = 'continuation';
        $getRequest->maxResults     = 999;
        $getRequest->timeout        = 888;
        $getRequest->returnTerms    = true;
        $getRequest->paginationSort = true;

        $this->client->expects($this->once())
            ->method('createRequest')
            ->with($this->equalTo('GET'), $this->equalTo($url))
            ->willReturn($request);

        $request->expects($this->once())
            ->method('setHeader')
            ->with(
                $this->equalTo('Accept'),
                $this->equalTo('application/json')
            );

        $request->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);

        $query->expects($this->exactly(7))
            ->method('add')
            ->will($this->returnValueMap([
                ['stream', 'true', $query],
                ['return_terms', 'true', $query],
                ['term_regex', 'regex', $query],
                ['max_results', 999, $query],
                ['continuation', 'continuation', $query],
                ['pagination_sort', 'true', $query],
                ['timeout', 888, $query],
            ]));

        $this->assertSame($request, $this->invokeMethod($this->instance, 'createHttpRequest', [$getRequest]));
    }

    /**
     * @expectedException Riak\Client\Core\Transport\RiakTransportException
     * @expectedExceptionMessage Unexpected status code : "555"
     */
    public function testUnexpectedHttpStatusCode()
    {
        $request      = new IndexQueryRequest();
        $httpQuery    = $this->getMock('GuzzleHttp\Query');
        $httpRequest  = $this->getMock('GuzzleHttp\Message\RequestInterface');
        $httpResponse = $this->getMock('GuzzleHttp\Message\ResponseInterface');

        $this->client->expects($this->once())
            ->method('createRequest')
            ->willReturn($httpRequest);

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($httpResponse);

        $httpRequest->expects($this->once())
            ->method('getQuery')
            ->willReturn($httpQuery);

        $httpQuery->expects($this->any())
            ->method('add')
            ->willReturn($httpRequest);

        $httpResponse->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(555);

        $request->type   = 'type';
        $request->bucket = 'bucket';

        $this->instance->send($request);
    }
}