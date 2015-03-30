<?php

namespace Riak\Client\Command\Kv\Builder;

/**
 * Used to construct a command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Builder
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Add an optional setting for this command.
     * This will be passed along with the request to Riak.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return \Riak\Client\Command\Builder\Builder
     */
    protected function withOption($option, $value)
    {
        $this->options[$option] = $value;

        return $this;
    }

    /**
     * Build a riak command object
     *
     * @return \Riak\Client\RiakCommand
     */
    abstract public function build();
}
