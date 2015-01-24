<?php

namespace RiakClientTest\Converter;

use RiakClientTest\TestCase;
use Riak\Client\Converter\ConverterFactory;
use RiakClientFixture\Domain\SimpleObject;

class ConverterFactoryTest extends TestCase
{
    /**
     * @var \Riak\Client\Converter\Hydrator\DomainHydrator
     */
    private $hydrator;

    /**
     * @var \Riak\Client\Converter\ConverterFactory
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->hydrator = $this->getMockBuilder('Riak\Client\Converter\Hydrator\DomainHydrator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->instance = new ConverterFactory($this->hydrator);
    }

    public function testCreateConverterIfNotExists()
    {
        $this->assertEmpty($this->instance->getConverters());

        $converter  = $this->instance->getConverter(SimpleObject::CLASS_NAME);
        $converters = $this->instance->getConverters();

        $this->assertInstanceOf('Riak\Client\Converter\Converter', $converter);
        $this->assertArrayHasKey(SimpleObject::CLASS_NAME, $converters);
        $this->assertSame($converter, $converters[SimpleObject::CLASS_NAME]);
    }

    public function testAddConverter()
    {
        $this->assertEmpty($this->instance->getConverters());

        $mock = $this->getMock('Riak\Client\Converter\Converter');

        $this->instance->addConverter(SimpleObject::CLASS_NAME, $mock);

        $converter  = $this->instance->getConverter(SimpleObject::CLASS_NAME);
        $converters = $this->instance->getConverters();

        $this->assertInstanceOf('Riak\Client\Converter\Converter', $converter);
        $this->assertArrayHasKey(SimpleObject::CLASS_NAME, $converters);
        $this->assertSame($converter, $converters[SimpleObject::CLASS_NAME]);
    }
}