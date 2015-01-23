<?php

namespace Riak\Client\Command\Bucket\Response;

use Riak\Client\RiakResponse;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\BucketProperties;

/**
 * Base Bucket Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Response implements RiakResponse
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
