<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Command\DataType\StoreCounter;
use Riak\Client\Command\DataType\CounterUpdate;

/**
 * Used to construct a StoreCounter command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreCounterBuilder extends StoreDataTypeBuilder
{
    /**
     * @var integer
     */
    private $delta;

    /**
     * @param integer $delta
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreCounterBuilder
     */
    public function withDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\StoreCounter
     */
    public function build()
    {
        return new StoreCounter($this->location, new CounterUpdate($this->delta), $this->context, $this->options);
    }
}
