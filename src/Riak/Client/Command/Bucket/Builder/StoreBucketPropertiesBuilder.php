<?php

namespace Riak\Client\Command\Bucket\Builder;

use Riak\Client\Command\Bucket\StoreBucketProperties;
use Riak\Client\Core\Query\RiakNamespace;

/**
 * Used to construct a StoreBucketProperties command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreBucketPropertiesBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $namespace;

    /**
     * @var array
     */
    private $properties;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param array                                 $properties
     */
    public function __construct(RiakNamespace $namespace = null, array $properties = [])
    {
        $this->namespace  = $namespace;
        $this->properties = $properties;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Add an propertu setting for this command.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\StoreBucketProperties
     */
    public function build()
    {
        return new StoreBucketProperties($this->namespace, $this->properties);
    }
}
