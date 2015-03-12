<?php

namespace Riak\Client\Command\Bucket\Response;

use Riak\Client\Core\Query\RiakNamespace;

/**
 * Store Bucket Properties Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreBucketPropertiesResponse extends Response
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Command\Bucket\Response\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace)
    {
        $this->namespace  = $namespace;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }
}
