<?php

namespace Riak\Client\Command\Kv\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\VClock;

/**
 * Used to construct a DeleteValue command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DeleteValueBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Riak\Client\Core\Query\VClock
     */
    private $vClock;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     */
    public function __construct(RiakLocation $location = null)
    {
        $this->location = $location;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     *
     * @return \Riak\Client\Command\Kv\Builder\DeleteValueBuilder
     */
    public function withLocation(RiakLocation $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param \Riak\Client\Core\Query\VClock $vClock
     *
     * @return \Riak\Client\Command\DeleteValue
     */
    public function withVClock(VClock $vClock)
    {
        $this->vClock = $vClock;

        return $this;
    }

    /**
     * Build a DeleteValue object
     *
     * @return \Riak\Client\Command\Kv\DeleteValue
     */
    public function build()
    {
        return new DeleteValue($this->location, $this->vClock, $this->options);
    }
}
