<?php

namespace Riak\Client\Command\Kv\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\RiakOption;

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
     * Set the r value.
     *
     * @param integer $r
     *
     * @return \Riak\Client\Command\Kv\Builder\FetchValueBuilder
     */
    public function withR($r)
    {
        return $this->withOption(RiakOption::R, $r);
    }

    /**
     * Set the pr value.
     *
     * @param integer $pr
     *
     * @return \Riak\Client\Command\Kv\Builder\FetchValueBuilder
     */
    public function withPr($pr)
    {
        return $this->withOption(RiakOption::PR, $pr);
    }

    /**
     * Set the basic_quorum value.
     *
     * @param boolean $use
     *
     * @return \Riak\Client\Command\Kv\Builder\FetchValueBuilder
     */
    public function withBasicQuorum($use)
    {
        return $this->withOption(RiakOption::BASIC_QUORUM, $use);
    }

    /**
     * Set the not_found_ok value.
     *
     * @param boolean $ok
     *
     * @return \Riak\Client\Command\Kv\Builder\FetchValueBuilder
     */
    public function withNotFoundOk($ok)
    {
        return $this->withOption(RiakOption::NOTFOUND_OK, $ok);
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
