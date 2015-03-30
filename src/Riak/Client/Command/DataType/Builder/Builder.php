<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Core\Query\RiakLocation;

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
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    protected $location;

    /**
     * @var string
     */
    protected $context;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location = null, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     *
     * @return \Riak\Client\Command\DataType\Builder\Builder
     */
    public function withLocation(RiakLocation $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Add an optional setting for this command.
     * This will be passed along with the request to Riak.
     *
     * @param string $option
     * @param mixed  $value
     *
     * @return \Riak\Client\Command\DataType\Builder\Builder
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
