<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Core\Query\Crdt\DataType;
use Riak\Client\Command\DataType\StoreSet;

/**
 * Used to construct a StoreSet command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSetBuilder extends Builder
{
    /**
     * @var array
     */
    private $adds = [];

    /**
     * @var array
     */
    private $removes = [];

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function add($value)
    {
        $this->adds[] = $value;

        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function remove($value)
    {
        $this->removes[] = $value;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function build()
    {
        $command = new StoreSet($this->location, $this->options);

        array_walk($this->adds, [$command, 'add']);
        array_walk($this->removes, [$command, 'remove']);

        return $command;
    }
}
