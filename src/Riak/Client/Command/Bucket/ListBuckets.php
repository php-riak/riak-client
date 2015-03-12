<?php

namespace Riak\Client\Command\Bucket;

use Riak\Client\RiakCommand;
use Riak\Client\Core\RiakCluster;
use Riak\Client\Command\Bucket\Builder\ListBucketsBuilder;
use Riak\Client\Core\Operation\Bucket\ListBucketsOperation;

/**
 * Command used to list the buckets contained in a bucket type.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListBuckets implements RiakCommand
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param string  $type
     * @param integer $timeout
     */
    public function __construct($type = null, $timeout = null)
    {
        $this->type    = $type;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(RiakCluster $cluster)
    {
        $operation = new ListBucketsOperation($this->type, $this->timeout);
        $response  = $cluster->execute($operation);

        return $response;
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
