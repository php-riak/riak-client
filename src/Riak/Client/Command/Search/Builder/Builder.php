<?php

namespace Riak\Client\Command\Search\Builder;

/**
 * Used to construct a search command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Builder
{
    /**
     * Build a riak search command
     *
     * @return \Riak\Client\RiakCommand
     */
    abstract public function build();
}
