<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Command\DataType\FetchCounter;

/**
 * Used to construct a FetchCounter command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchCounterBuilder extends FetchDataTypeBuilder
{
    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\FetchCounter
     */
    public function build()
    {
        return new FetchCounter($this->location, $this->options);
    }
}
