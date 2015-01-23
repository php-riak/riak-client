<?php

namespace Riak\Client\Command\Kv\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\Kv\FetchValue;

/**
 * Used to construct a FetchValue command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchValueBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

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
     * @return \Riak\Client\Command\Kv\Builder\FetchValueBuilder
     */
    public function withLocation(RiakLocation $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Build a FetchValue object
     *
     * @return \Riak\Client\Command\Kv\FetchValue
     */
    public function build()
    {
        return new FetchValue($this->location, $this->options);
    }
}
