<?php

namespace Riak\Client\Resolver;

/**
 * Simple factory for ConflictResolver objects.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ResolverFactory
{
    /**
     * @var \Riak\Client\Resolver\ConflictResolver[]
     */
    private $resolvers;

    /**
     * @var \Riak\Client\Resolver\DefaultConflictResolver
     */
    private $default;

    /**
     * Initialize the default resolver
     */
    public function __construct()
    {
        $this->default = new DefaultConflictResolver();
    }

    /**
     * @param \Riak\Client\Resolver\ConflictResolver[] $resolvers
     */
    public function setResolvers(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @return \Riak\Client\Resolver\ConflictResolver[]
     */
    public function getResolvers()
    {
        return $this->resolvers;
    }

    /**
     * @param string $class
     *
     * @return \Riak\Client\Resolver\ConflictResolver[]
     */
    public function getResolver($class)
    {
        if (isset($this->resolvers[$class])) {
            return $this->resolvers[$class];
        }

        return $this->default;
    }

    /**
     * @param string                                 $type
     * @param \Riak\Client\Resolver\ConflictResolver $resolver
     */
    public function addResolver($type, ConflictResolver $resolver)
    {
        $this->resolvers[$type] = $resolver;
    }
}
