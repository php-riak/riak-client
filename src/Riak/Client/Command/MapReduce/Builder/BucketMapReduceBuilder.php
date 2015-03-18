<?php

namespace Riak\Client\Command\MapReduce\Builder;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\KeyFilters;
use Riak\Client\Command\MapReduce\Specification;
use Riak\Client\Command\MapReduce\BucketMapReduce;
use Riak\Client\Command\MapReduce\Input\BucketInput;

/**
 * Used to construct a BucketMapReduce command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketMapReduceBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    /**
     * @var \Riak\Client\Command\MapReduce\KeyFilters
     */
    protected $filters;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace     $namespace
     * @param \Riak\Client\Command\MapReduce\KeyFilters $filters
     */
    public function __construct(RiakNamespace $namespace = null, KeyFilters $filters = null)
    {
        $this->namespace = $namespace;
        $this->filters   = $filters;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\MapReduce\Builder\BucketMapReduceBuilder
     */
    public function withNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param \Riak\Client\Command\MapReduce\KeyFilters $filters
     *
     * @return \Riak\Client\Command\MapReduce\Builder\BucketMapReduceBuilder
     */
    public function withKeyFilter(KeyFilters $filters)
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * Build a Bucket Map-Reduce command object
     *
     * @return \Riak\Client\Command\MapReduce\BucketMapReduce
     */
    public function build()
    {
        $input = new BucketInput($this->namespace, $this->filters);
        $spec  = new Specification($input, $this->phases, $this->timeout);

        return new BucketMapReduce($spec);
    }
}
