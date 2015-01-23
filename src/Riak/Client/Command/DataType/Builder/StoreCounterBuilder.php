<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\Crdt\RiakCounter;
use Riak\Client\Command\DataType\StoreCounter;

/**
 * Used to construct a StoreCounter command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreCounterBuilder extends Builder
{
    /**
     * @var integer
     */
    private $counter;

    /**
     * @param integer $delta
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreCounterBuilder
     */
    public function withDelta($delta)
    {
        $this->counter = $delta;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\StoreCounter
     */
    public function build()
    {
        $command = new StoreCounter($this->location, $this->options);

        if ($this->counter != null) {
            $command->withDelta($this->counter);
        }

        return $command;
    }
}
