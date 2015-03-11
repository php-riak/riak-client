<?php

namespace Riak\Client\Command\MapReduce\Input;

use Riak\Client\Core\Query\RiakNamespace;

/**
 * Map-Reduce bucket input
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketInput implements MapReduceInput
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var array
     */
    private $filters;

    /**
     * @param \Riak\Client\Command\MapReduce\Input\RiakNamespace $namespace
     * @param array                                              $filters
     */
    public function __construct(RiakNamespace $namespace, array $filters = [])
    {
        $this->namespace = $namespace;
        $this->filters   = $filters;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\Input\RiakNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $filters = [];
        $bucket  = ( ! $this->namespace->isDefaultType())
            ? [$this->namespace->getBucketType(), $this->namespace->getBucketName()]
            : $this->namespace->getBucketName();

        return [
            'bucket'      => $bucket,
            'key_filters' => $filters
        ];
    }
}
