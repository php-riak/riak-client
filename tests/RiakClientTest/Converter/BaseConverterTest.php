<?php

namespace RiakClientTest\Converter;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Converter\RiakObjectReference;
use Riak\Client\Converter\DomainObjectReference;
use RiakClientFixture\Domain\SimpleObject;


class BaseConverterTest extends TestCase
{
    /**
     * @var \Riak\Client\Converter\Hydrator\DomainHydrator
     */
    private $hydrator;

    /**
     * @var \Riak\Client\Converter\BaseConverter
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->hydrator = $this->getMockBuilder('Riak\Client\Converter\Hydrator\DomainHydrator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = $this->getMockForAbstractClass('Riak\Client\Converter\BaseConverter', [$this->hydrator]);
    }

    public function testFromDomain()
    {
        $domainObject = new SimpleObject();
        $namespace    = new RiakNamespace('type', 'bucket');
        $location     = new RiakLocation($namespace, 'riak-key');
        $reference    = new DomainObjectReference($domainObject, $location);
        $callback     = function($subject){
            return ($subject instanceof \Riak\Client\Core\Query\RiakObject);
        };

        $this->instance->expects($this->once())
            ->method('fromDomainObject')
            ->with($this->equalTo($domainObject))
            ->willReturn('{"value":[1,2,3]}');

        $this->hydrator->expects($this->once())
            ->method('setRiakObjectValues')
            ->with($this->callback($callback), $this->equalTo($domainObject), $this->equalTo($location));

        $riakObject = $this->instance->fromDomain($reference);

        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('{"value":[1,2,3]}', $riakObject->getValue());
    }

    public function testToDomain()
    {
        $riakObject   = new RiakObject();
        $domainObject = new SimpleObject('[1,2,3]');
        $namespace    = new RiakNamespace('type', 'bucket');
        $location     = new RiakLocation($namespace, 'riak-key');
        $reference    = new RiakObjectReference($riakObject, $location, SimpleObject::CLASS_NAME);

        $riakObject->setValue('{"value":[1,2,3]}');

        $this->instance->expects($this->once())
            ->method('toDomainObject')
            ->with($this->equalTo('{"value":[1,2,3]}'), $this->equalTo(SimpleObject::CLASS_NAME))
            ->willReturn($domainObject);

        $this->hydrator->expects($this->once())
            ->method('setDomainObjectValues')
            ->with($this->equalTo($domainObject), $this->equalTo($riakObject), $this->equalTo($location));

        $this->assertSame($domainObject, $this->instance->toDomain($reference));
    }
}