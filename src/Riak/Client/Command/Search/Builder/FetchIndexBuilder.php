<?php

namespace Riak\Client\Command\Search\Builder;

use Riak\Client\Command\Search\FetchIndex;

/**
 * Used to construct a FetchIndex command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchIndexBuilder extends Builder
{
    /**
     * @var string
     */
    private $indexName;

    /**
     * @param string $indexName
     */
    public function __construct($indexName = null)
    {
        $this->indexName = $indexName;
    }

    /**
     * @param string $schemaName
     *
     * @return \Riak\Client\Command\Search\Builder\FetchIndexBuilder
     */
    public function withIndexName($schemaName)
    {
        $this->indexName = $schemaName;

        return $this;
    }

    /**
     * Build a FetchIndex object
     *
     * @return \Riak\Client\Command\Search\FetchIndex
     */
    public function build()
    {
        return new FetchIndex($this->indexName);
    }
}
