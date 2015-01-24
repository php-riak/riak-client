<?php

namespace Riak\Client\Core\Operation\Bucket;

use Riak\Client\Command\Bucket\Response\StoreBucketPropertiesResponse;
use Riak\Client\Core\Message\Bucket\PutRequest;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\RiakOperation;
use Riak\Client\Core\RiakTransport;

/**
 * An operation used to store bucket properties in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StorePropertiesOperation implements RiakOperation
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var array
     */
    private $properties;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace        $namespace
     * @param \Riak\Client\Core\Query\RiakBucketProperties $properties
     */
    public function __construct(RiakNamespace $namespace, array $properties)
    {
        $this->namespace  = $namespace;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakTransport $adapter)
    {
        $adapter->send($this->createGetRequest());

        return new StoreBucketPropertiesResponse($this->namespace);
    }

    /**
     * @return \Riak\Client\Core\Message\Bucket\PutRequest
     */
    private function createGetRequest()
    {
        $request = new PutRequest();

        $request->type   = $this->namespace->getBucketType();
        $request->bucket = $this->namespace->getBucketName();

        foreach ($this->properties as $name => $value) {
            $request->{$name} = $value;
        }

        return $request;
    }
}
