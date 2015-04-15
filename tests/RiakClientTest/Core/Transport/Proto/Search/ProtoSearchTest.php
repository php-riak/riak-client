<?php

namespace RiakClientTest\Core\Transport\Proto\Bucket;

use Riak\Client\Core\Transport\Proto\Search\ProtoSearch;
use Riak\Client\Core\Message\Search\SearchRequest;
use Riak\Client\ProtoBuf\RpbSearchQueryResp;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\ProtoBuf\RpbSearchDoc;
use Riak\Client\ProtoBuf\RpbPair;
use RiakClientTest\TestCase;

class ProtoSearchTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\Transport\Proto\Search\ProtoSearch
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->client   = $this->getMock('Riak\Client\Core\Transport\Proto\ProtoClient', [], [], '', false);
        $this->instance = new ProtoSearch($this->client);
    }

    /**
     * @param string $key
     * @param string $val
     *
     * @return \Riak\Client\ProtoBuf\RpbPair
     */
    public function createRpbPair($key, $val)
    {
        $pair = new RpbPair();

        $pair->key   = $key;
        $pair->value = $val;

        return $pair;
    }

    public function testCreateRpbMessage()
    {
        $request = new SearchRequest();

        $request->start   = 1;
        $request->rows    = 10;
        $request->op      = 'and';
        $request->sort    = 'name';
        $request->df      = 'name';
        $request->presort = 'score';
        $request->q       = 'name:Fabio*';
        $request->index   = 'index-name';
        $request->filter  = 'age:[18 TO *]';
        $request->fl      = ['name', 'age'];

        $message = $this->invokeMethod($this->instance, 'createRpbMessage', [$request]);

        $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbSearchQueryReq', $message);
        $this->assertEquals('age:[18 TO *]', $message->filter);
        $this->assertEquals('index-name', $message->index);
        $this->assertEquals('name:Fabio*', $message->q);
        $this->assertEquals('score', $message->presort);
        $this->assertEquals('name', $message->sort);
        $this->assertEquals('name', $message->df);
        $this->assertEquals('and', $message->op);
        $this->assertEquals(10, $message->rows);
        $this->assertEquals(1, $message->start);
        $this->assertCount(2, $message->fl);
        $this->assertContains('age', $message->fl);
        $this->assertContains('name', $message->fl);
    }

    public function testSearchMessageResponse()
    {
        $rpbResp  = new RpbSearchQueryResp();
        $rpbDoc1  = new RpbSearchDoc();
        $rpbDoc2  = new RpbSearchDoc();
        $request  = new SearchRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbSearchQueryReq', $subject);
            $this->assertEquals('index-name', $subject->index);
            $this->assertEquals('name:Fabio*', $subject->q);

            return true;
        };

        $request->q     = 'name:Fabio*';
        $request->index = 'index-name';

        $rpbResp->setMaxScore(1);
        $rpbResp->setNumFound(2);
        $rpbResp->addDocs($rpbDoc1);
        $rpbResp->addDocs($rpbDoc2);
        $rpbDoc1->addFields($this->createRpbPair('name', 'Fabio B. Silva'));
        $rpbDoc2->addFields($this->createRpbPair('name', 'Fabio B. Silva'));
        $rpbDoc1->addFields($this->createRpbPair('username', 'fabios'));
        $rpbDoc2->addFields($this->createRpbPair('username', 'FabioBatSilva'));

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::SEARCH_QUERY_REQ),
                $this->equalTo(RiakMessageCodes::SEARCH_QUERY_RESP)
            );

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

    public function testSearchMessageResponseNull()
    {
        $rpbResp  = new RpbSearchQueryResp();
        $request  = new SearchRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbSearchQueryReq', $subject);
            $this->assertEquals('index-name', $subject->index);
            $this->assertEquals('name:Fabio*', $subject->q);

            return true;
        };

        $request->q     = 'name:Fabio*';
        $request->index = 'index-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn($rpbResp)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::SEARCH_QUERY_REQ),
                $this->equalTo(RiakMessageCodes::SEARCH_QUERY_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\SearchResponse', $this->instance->send($request));
    }

    public function testSearchMessageResponseEnptyDocs()
    {
        $request  = new SearchRequest();
        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\ProtoBuf\RpbSearchQueryReq', $subject);
            $this->assertEquals('index-name', $subject->index);
            $this->assertEquals('name:Fabio*', $subject->q);

            return true;
        };

        $request->q     = 'name:Fabio*';
        $request->index = 'index-name';

        $this->client->expects($this->once())
            ->method('send')
            ->willReturn(null)
            ->with(
                $this->callback($callback),
                $this->equalTo(RiakMessageCodes::SEARCH_QUERY_REQ),
                $this->equalTo(RiakMessageCodes::SEARCH_QUERY_RESP)
            );

        $this->assertInstanceOf('Riak\Client\Core\Message\Search\SearchResponse', $this->instance->send($request));
    }

    public function testDocToArray()
    {
        $rpbDoc = new RpbSearchDoc();

        $rpbDoc->addFields($this->createRpbPair('name', 'Fabio B. Silva'));
        $rpbDoc->addFields($this->createRpbPair('username', 'FabioBatSilva'));

        $result = $this->invokeMethod($this->instance, 'docToArray', [$rpbDoc]);

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('username', $result);

        $this->assertEquals(['Fabio B. Silva'], $result['name']);
        $this->assertEquals(['FabioBatSilva'], $result['username']);
    }

    public function testDocToArrayMulti()
    {
        $rpbDoc = new RpbSearchDoc();

        $rpbDoc->addFields($this->createRpbPair('multi_ss', '1'));
        $rpbDoc->addFields($this->createRpbPair('multi_ss', '2'));
        $rpbDoc->addFields($this->createRpbPair('multi_ss', '3'));

        $result = $this->invokeMethod($this->instance, 'docToArray', [$rpbDoc]);

        $this->assertArrayHasKey('multi_ss', $result);
        $this->assertEquals(['1', '2', '3'], $result['multi_ss']);
    }

    public function testEnptyDocToArray()
    {
        $this->assertEquals([], $this->invokeMethod($this->instance, 'docToArray', [new RpbSearchDoc()]));
    }
}