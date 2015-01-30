<?php

namespace Riak\Client\Command\Search\Builder;

use Riak\Client\Command\Search\DeleteIndex;

/**
 * Used to construct a DeleteIndex command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteIndexBuilder extends Builder
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
     * @return \Riak\Client\Command\Search\Builder\DeleteIndexBuilder
     */
    public function withIndexName($schemaName)
    {
        $this->indexName = $schemaName;

        return $this;
    }

    /**
     * Build a DeleteIndex object
     *
     * @return \Riak\Client\Command\Search\DeleteIndex
     */
    public function build()
    {
        return new DeleteIndex($this->indexName);
    }
}
