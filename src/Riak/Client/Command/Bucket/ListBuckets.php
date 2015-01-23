<?php

namespace Riak\Client\Command\Bucket;

use Riak\Client\RiakCommand;
use Riak\Client\RiakException;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Command\Bucket\Builder\ListBucketsBuilder;

/**
 * Command used to list the buckets contained in a bucket type.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListBuckets implements RiakCommand
{
    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        throw new RiakException("Not implemented");
    }

    /**
     * @param string $type
     *
     * @return \Riak\Client\Command\Bucket\Builder\ListBucketsBuilder
     */
    public static function builder($type = null)
    {
        return new ListBucketsBuilder($type);
    }
}
