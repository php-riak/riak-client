<?php

namespace Riak\Client\Command\Bucket\Response;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\BucketProperties;

/**
 * Fetch Bucket Properties Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchBucketPropertiesResponse extends Response
{
    /**
     * @var \Riak\Client\Core\Query\BucketProperties
     */
    private $properties;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Command\Bucket\Response\RiakNamespace $namespace
     * @param \Riak\Client\Core\Query\BucketProperties           $properties
     */
    public function __construct(RiakNamespace $namespace, BucketProperties $properties)
    {
        $this->namespace  = $namespace;
        $this->properties = $properties;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return \Riak\Client\Core\Query\BucketProperties
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
