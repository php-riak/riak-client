<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Command\DataType\FetchMap;

/**
 * Used to construct a FetchMap command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchMapBuilder extends FetchDataTypeBuilder
{
    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\FetchMap
     */
    public function build()
    {
        return new FetchMap($this->location, $this->options);
    }
}
