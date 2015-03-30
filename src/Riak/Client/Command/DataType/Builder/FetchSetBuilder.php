<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Command\DataType\FetchSet;

/**
 * Used to construct a FetchSet command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSetBuilder extends FetchDataTypeBuilder
{
    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\FetchSet
     */
    public function build()
    {
        return new FetchSet($this->location, $this->options);
    }
}
