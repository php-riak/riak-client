<?php

namespace RiakClientTest\Command\Kv;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Message\Kv\Content;
use Riak\Client\Core\Query\RiakNamespace;
use RiakClientFixture\Domain\SimpleObject;
use Riak\Client\Core\Message\Kv\GetResponse;
use RiakClientFixture\Resolver\SimpleObjectConflictResolver;

class FetchDomainValueWithConflicResolverTest extends TestCase
{
    private $location;
    private $client;
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder  = new RiakClientBuilder();
        $resolver = new SimpleObjectConflictResolver();

        $this->location = new RiakLocation(new RiakNamespace('type', 'bucket'), 'key');
        $this->adapter  = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->node     = new RiakNode($this->adapter);
        $this->client   = $builder
            ->withConflictResolver(SimpleObject::CLASS_NAME, $resolver)
            ->withNode($this->node)
            ->build();
    }

    public function testFetchUsingConflicResolver()
    {
        $getResponse = new GetResponse();
        $command     = FetchValue::builder()
            ->withLocation($this->location)
            ->build();

        $c1 = new Content();
        $c2 = new Content();

        $getResponse->vClock      = 'vclock-hash';
        $getResponse->contentList = [$c1, $c2];

        $c1->contentType   = 'application/json';
        $c1->lastModified  = 1420246861;
        $c1->value         = '{"value":"line 1"}';

        $c2->contentType   = 'application/json';
        $c2->lastModified  = 1420250522;
        $c2->value         = '{"value":"line 2"}';

        $this->adapter->expects($this->once())
            ->method('send')
            ->will($this->returnValue($getResponse));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\VClock', $result->getVectorClock());

        $this->assertTrue($result->hasValues());
        $this->assertFalse($result->getNotFound());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals(2, $result->getNumberOfValues());
        $this->assertEquals('vclock-hash', $result->getVectorClock()->getValue());

        $values = $result->getValues();
        $domain = $result->getValue(SimpleObject::CLASS_NAME);

        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $domain);
        $this->assertNotSame($domain, $values[0]);
        $this->assertNotSame($domain, $values[1]);
        $this->assertEquals("line 1" . PHP_EOL . "line 2", $domain->getValue());
    }
}