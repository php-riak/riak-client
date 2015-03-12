<?php

namespace Riak\Client\Command\Bucket\Builder;

use Riak\Client\Command\Bucket\FetchBucketProperties;
use Riak\Client\Core\Query\RiakNamespace;

/**
 * Used to construct a FetchBucketProperties command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchBucketPropertiesBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace = null)
    {
        $this->namespace = $namespace;
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
     * Build a command object
     *
     * @return \Riak\Client\Command\Bucket\FetchBucketProperties
     */
    public function build()
    {
        return new FetchBucketProperties($this->namespace);
    }
}
