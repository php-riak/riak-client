<?php

namespace Riak\Client\Command\Bucket\Builder;

/**
 * Used to construct a command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Builder
{
    /**
     * Build a riak command object
     *
     * @return \Riak\Client\RiakCommand
     */
    abstract public function build();
}
