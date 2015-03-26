<?php

namespace Riak\Client\Command\MapReduce\Input;

/**
 * Map-Reduce search input
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchInput implements MapReduceInput
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
    public function __construct($index, $query)
    {
        $this->index = $index;
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'module'   => 'yokozuna',
            'function' => 'mapred_search',
            'arg'      => [$this->index, $this->query]
        ];
    }
}
