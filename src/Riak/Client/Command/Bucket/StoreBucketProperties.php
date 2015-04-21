<?php

namespace Riak\Client\Command\Bucket;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Operation\Bucket\StorePropertiesOperation;
use Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder;

/**
 * Command used to store the properties of a bucket in Riak.
 *
 * Example:
 * <code>
 * <?php
 *  use Riak\Client\Core\Query\RiakNamespace;
 *  use Riak\Client\Command\Bucket\StoreBucketProperties;
 *
 *  $namespace = new RiakNamespace('bucket_type', 'bucket_name');
 *  $command   = StoreBucketProperties::builder()
 *      ->withNamespace($namespace)
 *      ->withLastWriteWins(false)
 *      ->withNotFoundOk(true)
 *      ->withAllowMulti(true)
 *      ->build();
 *
 *  // @var $response \Riak\Client\Command\Bucket\Response\StoreBucketPropertiesResponse
 *  $response = $client->execute($command);
 * </code>
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreBucketProperties implements RiakCommand
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
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param array                                 $properties
     */
    public function __construct(RiakNamespace $namespace, array $properties = [])
    {
        $this->namespace  = $namespace;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new StorePropertiesOperation($this->namespace, $this->properties);
        $response  = $cluster->execute($operation);

        return $response;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public static function builder(RiakNamespace $namespace = null)
    {
        return new StoreBucketPropertiesBuilder($namespace);
    }
}
