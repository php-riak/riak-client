<?php

namespace RiakClientTest\Command\Kv\Response;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\VClock;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\RiakObjectList;
use Riak\Client\Converter\ConverterFactory;
use Riak\Client\Resolver\ResolverFactory;
use RiakClientFixture\Domain\SimpleObject;

class AbstractResponseTest extends TestCase
{/**
     * @var \Riak\Client\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Riak\Client\Resolver\ResolverFactory
     */
    private $resolverFactory;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Riak\Client\Converter\Hydrator\DomainHydrator
     */
    private $hydrator;

    protected function setUp()
    {
        parent::setUp();

        $this->hydrator         = $this->getMock('Riak\Client\Converter\Hydrator\DomainHydrator', [], [], '', false);
        $this->resolverFactory  = new ResolverFactory();
        $this->converterFactory = new ConverterFactory($this->hydrator);
        $this->location         = new RiakLocation(new RiakNamespace('type', 'bucket'), 'key');
    }

    public function testResponse()
    {
        $object   = new RiakObject();
        $vClock   = new VClock('vclock-hash');
        $values   = new RiakObjectList([$object]);
        $instance = $this->getMockForAbstractClass('Riak\Client\Command\Kv\Response\ObjectResponse', [
            $this->converterFactory,
            $this->resolverFactory,
            $this->location,
            $values
        ]);

        $object->setVClock($vClock);

        $this->assertSame($this->location, $instance->getLocation());
        $this->assertEquals(1, $instance->getNumberOfValues());
        $this->assertSame($vClock, $instance->getVectorClock());
        $this->assertSame($values, $instance->getValues());
        $this->assertSame($object, $instance->getValue());
        $this->assertTrue($instance->hasValues());
    }

    public function testResponseConverter()
    {
        $object   = new RiakObject();
        $vClock   = new VClock('vclock-hash');
        $list     = new RiakObjectList([$object]);
        $instance = $this->getMockForAbstractClass('Riak\Client\Command\Kv\Response\ObjectResponse', [
            $this->converterFactory,
            $this->resolverFactory,
            $this->location,
            $list
        ]);

        $object->setVClock($vClock);
        $object->setVClock('{"value":[1,1,1]}');
        $object->setContentType('application/json');

        $riakObjectList   = $instance->getValues();
        $domainObjectList = $instance->getValues(SimpleObject::CLASS_NAME);

        $riakObject       = $instance->getValue();
        $domainObject     = $instance->getValue(SimpleObject::CLASS_NAME);

        $this->assertCount(1, $riakObjectList);
        $this->assertCount(1, $domainObjectList);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $domainObject);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $riakObject);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObjectList', $riakObjectList);
        $this->assertInstanceOf('Riak\Client\Core\Query\DomainObjectList', $domainObjectList);
    }

    public function testEmptyList()
    {
        $values   = new RiakObjectList([]);
        $instance = $this->getMockForAbstractClass('Riak\Client\Command\Kv\Response\ObjectResponse', [
            $this->converterFactory,
            $this->resolverFactory,
            $this->location,
            $values
        ]);

        $this->assertSame($this->location, $instance->getLocation());
        $this->assertEquals(0, $instance->getNumberOfValues());
        $this->assertSame($values, $instance->getValues());
        $this->assertNull($instance->getVectorClock());
        $this->assertFalse($instance->hasValues());
        $this->assertNull($instance->getValue());
    }
}