<?php

namespace Riak\Client\Command\Bucket;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Operation\Bucket\FetchPropertiesOperation;
use Riak\Client\Command\Bucket\Builder\FetchBucketPropertiesBuilder;

/**
 * Command used to fetch the properties of a bucket in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchBucketProperties implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new FetchPropertiesOperation($this->namespace);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\Bucket\Builder\FetchBucketPropertiesBuilder
     */
    public static function builder(RiakNamespace $namespace = null)
    {
        return new FetchBucketPropertiesBuilder($namespace);
    }
}
