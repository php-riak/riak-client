<?php

namespace RiakClientTest\Resolver;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakObjectList;
use Riak\Client\Resolver\DefaultConflictResolver;

class DefaultConflictResolverTest extends TestCase
{
    /**
     * @var \Riak\Client\Resolver\DefaultConflictResolver
     */
    private $instance;

    protected function setUp()
    {
        parent::setUp();

        $this->instance = new DefaultConflictResolver();
    }

    public function testResolverSingleObject()
    {
        $object   = new RiakObject();
        $siblings = new RiakObjectList([$object]);
        $resolved = $this->instance->resolve($siblings);

        $this->assertSame($object, $resolved);
    }

    public function testResolverEmptyList()
    {
        $siblings = new RiakObjectList([]);
        $resolved = $this->instance->resolve($siblings);

        $this->assertNull($resolved);
    }

    /**
     * @expectedException Riak\Client\Resolver\UnresolvedConflictException
     */
    public function testUnresolvedConflictException()
    {
        $object1  = new RiakObject();
        $object2  = new RiakObject();
        $siblings = new RiakObjectList([$object1, $object2]);

        $this->instance->resolve($siblings);
    }
}