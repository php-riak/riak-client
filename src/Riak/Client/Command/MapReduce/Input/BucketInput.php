<?php

namespace Riak\Client\Command\MapReduce\Input;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\KeyFilters;

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
     * @var \Riak\Client\Command\MapReduce\KeyFilters
     */
    private $filters;

    /**
     * @param \Riak\Client\Command\MapReduce\Input\RiakNamespace $namespace
     * @param \Riak\Client\Command\MapReduce\KeyFilters          $filters
     */
    public function __construct(RiakNamespace $namespace, KeyFilters $filters = null)
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
     * @return \Riak\Client\Command\MapReduce\KeyFilters
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
        $filters = $this->filters ?: [];
        $bucket  = ( ! $this->namespace->isDefaultType())
            ? [$this->namespace->getBucketType(), $this->namespace->getBucketName()]
            : $this->namespace->getBucketName();

        return [
            'bucket'      => $bucket,
            'key_filters' => $filters
        ];
    }
}
