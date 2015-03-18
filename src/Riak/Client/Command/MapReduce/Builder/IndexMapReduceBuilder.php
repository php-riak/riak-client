<?php

namespace Riak\Client\Command\MapReduce\Builder;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\Specification;
use Riak\Client\Command\MapReduce\IndexMapReduce;
use Riak\Client\Command\MapReduce\Input\IndexInput;
use Riak\Client\Command\MapReduce\Input\Index\RangeCriteria;
use Riak\Client\Command\MapReduce\Input\Index\MatchCriteria;

/**
 * Used to construct a IndexMapReduce command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexMapReduceBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var \Riak\Client\Command\MapReduce\Input\IndexCriteria
     */
    protected $criteria;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace = null)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder
     */
    public function withNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * @param string $index
     *
     * @return \Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder
     */
    public function withIndexInt($index)
    {
        $this->indexName = $index  . '_int';

        return $this;
    }

    /**
     * @param string $index
     *
     * @return \Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder
     */
    public function withIndexBin($index)
    {
        $this->indexName = $index  . '_bin';

        return $this;
    }

    /**
     * @param mixed $start
     * @param mixed $end
     *
     * @return \Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder
     */
    public function withRange($start, $end)
    {
        $this->criteria = new RangeCriteria($start, $end);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return \Riak\Client\Command\MapReduce\Builder\IndexMapReduceBuilder
     */
    public function withMatchValue($value)
    {
        $this->criteria = new MatchCriteria($value);

        return $this;
    }

    /**
     * Build a Index Map-Reduce command object
     *
     * @return \Riak\Client\Command\MapReduce\IndexMapReduce
     */
    public function build()
    {
        $input = new IndexInput($this->namespace, $this->indexName, $this->criteria);
        $spec  = new Specification($input, $this->phases, $this->timeout);

        return new IndexMapReduce($spec);
    }
}
