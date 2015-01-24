<?php

namespace RiakClientTest\Resolver;

use RiakClientTest\TestCase;
use Riak\Client\Resolver\ResolverFactory;
use RiakClientFixture\Domain\SimpleObject;

class ResolverFactoryTest extends TestCase
{
    /**
     * @var \Riak\Client\Resolver\ResolverFactory
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new ResolverFactory();
    }

    public function testUseDefaultResolverIfNotDefined()
    {
        $this->assertEmpty($this->instance->getResolvers());

        $resolver = $this->instance->getResolver(SimpleObject::CLASS_NAME);

        $this->assertInstanceOf('Riak\Client\Resolver\DefaultConflictResolver', $resolver);
    }

    public function testAddResolver()
    {
        $this->assertEmpty($this->instance->getResolvers());

        $mock = $this->getMock('Riak\Client\Resolver\ConflictResolver');

        $this->instance->addResolver(SimpleObject::CLASS_NAME, $mock);

        $this->assertSame($mock, $this->instance->getResolver(SimpleObject::CLASS_NAME));
    }
}