<?php

namespace Riak\Client\Command\Bucket\Builder;

use Riak\Client\Command\Bucket\ListBuckets;

/**
 * Used to construct a ListBuckets command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListBucketsBuilder extends Builder
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
     * @param string $type
     */
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * Set the bucket type.
     *
     * @param string $type
     *
     * @return \Riak\Client\Command\Bucket\Builder\ListBucketsBuilder
     */
    public function withBucketType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the Riak-side timeout value.
     *
     * @param mixed  $timeout
     *
     * @return \Riak\Client\Command\Bucket\Builder\ListBucketsBuilder
     */
    public function withTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\Bucket\FetchBucketProperties
     */
    public function build()
    {
        return new ListBuckets($this->type, $this->timeout);
    }
}
