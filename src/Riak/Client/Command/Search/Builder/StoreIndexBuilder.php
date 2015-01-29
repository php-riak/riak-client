<?php

namespace Riak\Client\Command\Search\Builder;

use Riak\Client\Command\Search\StoreIndex;
use Riak\Client\Core\Query\Search\YokozunaIndex;

/**
 * Used to construct a StoreIndex command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreIndexBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\Search\YokozunaIndex
     */
    private $index;

    /**
     * @param \Riak\Client\Core\Query\Search\YokozunaIndex $index
     */
    public function __construct(YokozunaIndex $index = null)
    {
        $this->index = $index;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $schema
     *
     * @return \Riak\Client\Command\Search\Builder\StoreIndexBuilder
     */
    public function withIndex(YokozunaIndex $schema)
    {
        $this->index = $schema;

        return $this;
    }

    /**
     * Build a StoreIndex object
     *
     * @return \Riak\Client\Command\Search\StoreIndex
     */
    public function build()
    {
        return new StoreIndex($this->index);
    }
}
