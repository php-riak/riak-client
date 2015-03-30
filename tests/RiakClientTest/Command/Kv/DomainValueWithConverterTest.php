<?php

namespace RiakClientTest\Command\Kv;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Message\Kv\Content;
use Riak\Client\Core\Query\RiakNamespace;
use RiakClientFixture\Domain\DomainObject;
use Riak\Client\Core\Message\Kv\GetResponse;
use Riak\Client\Core\Message\DataType\PutResponse;
use Doctrine\Common\Annotations\AnnotationReader;
use Riak\Client\Converter\Hydrator\DomainHydrator;
use RiakClientFixture\Converter\DomainObjectConverter;
use Riak\Client\Converter\Hydrator\DomainMetadataReader;

class DomainValueWithConverterTest extends TestCase
{
    private $transport;
    private $location;
    private $client;

    protected function setUp()
    {
        parent::setUp();

        $metadataReader = new DomainMetadataReader(new AnnotationReader());
        $domainHydrator = new DomainHydrator($metadataReader);
        $builder        = new RiakClientBuilder();
        $converter      = new DomainObjectConverter($domainHydrator);

        $this->location = new RiakLocation(new RiakNamespace('type', 'bucket'), 'key');
        $this->transport  = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->node     = new RiakNode($this->transport);
        $this->client   = $builder
            ->withConverter(DomainObject::CLASS_NAME, $converter)
            ->withNode($this->node)
            ->build();
    }

    public function testStoreUsingConverter()
    {
        $domainObject = new DomainObject([1,2,3]);
        $response     = new PutResponse();
        $command      = StoreValue::builder()
            ->withLocation($this->location)
            ->withValue($domainObject)
            ->build();

        $respContent = new Content();

        $response->vClock      = 'vclock-hash';
        $response->contentList = [$respContent];

        $respContent->contentType   = 'application/json';
        $respContent->lastModified  = 1420246861;
        $respContent->value         = '1,2,3';

        $callback = function($subject) {
            $this->assertInstanceOf('Riak\Client\Core\Message\Kv\PutRequest', $subject);
            $this->assertInstanceOf('Riak\Client\Core\Message\Kv\Content', $subject->content);
            $this->assertEquals('plain/text', $subject->content->contentType);
            $this->assertEquals('1,2,3', $subject->content->value);

            return true;
        };

        $this->transport->expects($this->once())
            ->method('send')
            ->with($this->callback($callback))
            ->will($this->returnValue($response));

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\StoreValueResponse', $this->client->execute($command));
    }

    public function testFetchUsingConverter()
    {
        $getResponse = new GetResponse();
        $command     = FetchValue::builder()
            ->withLocation($this->location)
            ->build();

        $content = new Content();

        $getResponse->vClock      = 'vclock-hash';
        $getResponse->contentList = [$content];

        $content->contentType   = 'application/json';
        $content->lastModified  = 1420246861;
        $content->value         = '1,2,3';

        $this->transport->expects($this->once())
            ->method('send')
            ->will($this->returnValue($getResponse));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\VClock', $result->getVectorClock());

        $this->assertTrue($result->hasValues());
        $this->assertFalse($result->getNotFound());
        $this->assertCount(1, $result->getValues());
        $this->assertEquals(1, $result->getNumberOfValues());
        $this->assertEquals('vclock-hash', $result->getVectorClock()->getValue());

        $domain = $result->getValue(DomainObject::CLASS_NAME);

        $this->assertInstanceOf(DomainObject::CLASS_NAME, $domain);
        $this->assertEquals([1,2,3], $domain->getValues());
    }
}