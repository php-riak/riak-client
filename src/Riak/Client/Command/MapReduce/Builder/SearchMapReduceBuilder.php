<?php

namespace Riak\Client\Command\MapReduce\Builder;

use Riak\Client\Command\MapReduce\Specification;
use Riak\Client\Command\MapReduce\SearchMapReduce;
use Riak\Client\Command\MapReduce\Input\SearchInput;

/**
 * Used to construct a SearchMapReduce command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchMapReduceBuilder extends Builder
{
    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $index;

    /**
     * @param string $index
     * @param string $query
     */
    public function __construct($index = null, $query = null)
    {
        $this->index = $index;
        $this->query = $query;
    }

    /**
     * @param string $index
     *
     * @return \Riak\Client\Command\MapReduce\Builder\SearchMapReduceBuilder
     */
    public function withIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @param string $query
     *
     * @return \Riak\Client\Command\MapReduce\Builder\SearchMapReduceBuilder
     */
    public function withQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Build a Bucket Map-Reduce command object
     *
     * @return \Riak\Client\Command\MapReduce\SearchMapReduce
     */
    public function build()
    {
        $input = new SearchInput($this->index, $this->query);
        $spec  = new Specification($input, $this->phases, $this->timeout);

        return new SearchMapReduce($spec);
    }
}
