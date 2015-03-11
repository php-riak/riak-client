<?php

namespace Riak\Client\Command\MapReduce\Input;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\Input\Index\IndexCriteria;

/**
 * Map-Reduce index input
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexInput implements MapReduceInput
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var string
     */
    private $indexName;

    /**
     * @var \Riak\Client\Command\MapReduce\Input\IndexCriteria
     */
    private $criteria;

    /**
     * @param \Riak\Client\Command\MapReduce\Input\RiakNamespace $namespace
     * @param string                                             $indexName
     * @param \Riak\Client\Command\MapReduce\Input\IndexCriteria $criteria
     */
    public function __construct(RiakNamespace $namespace, $indexName, IndexCriteria $criteria = null)
    {
        $this->namespace = $namespace;
        $this->indexName = $indexName;
        $this->criteria  = $criteria;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\Input\RiakNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\Input\IndexCriteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $index    = $this->indexName;
        $criteria = $this->criteria
            ? $this->criteria->jsonSerialize()
            : [];

        $bucket = ( ! $this->namespace->isDefaultType())
            ? [$this->namespace->getBucketType(), $this->namespace->getBucketName()]
            : $this->namespace->getBucketName();

        return array_merge([
            'bucket' => $bucket,
            'index'  => $index
        ], $criteria);
    }
}
