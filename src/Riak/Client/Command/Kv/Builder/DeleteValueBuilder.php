<?php

namespace Riak\Client\Command\Kv\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\VClock;
use Riak\Client\RiakOption;

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
     * Set the pw value.
     *
     * @param integer $pw
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withPw($pw)
    {
        return $this->withOption(RiakOption::PW, $pw);
    }

    /**
     * Set the rw value.
     *
     * @param integer $rw
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreRiakOptionBuilder
     */
    public function withRw($rw)
    {
        return $this->withOption(RiakOption::RW, $rw);
    }

    /**
     * Set the dw value.
     *
     * @param integer $dw
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreRiakOptionBuilder
     */
    public function withDw($dw)
    {
        return $this->withOption(RiakOption::DW, $dw);
    }

    /**
     * Set the w value.
     *
     * @param integer $w
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreRiakOptionBuilder
     */
    public function withW($w)
    {
        return $this->withOption(RiakOption::W, $w);
    }

    /**
     * Set the r value.
     *
     * @param integer $r
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreRiakOptionBuilder
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
     * @return \Riak\Client\Command\Bucket\Builder\StoreRiakOptionBuilder
     */
    public function withPr($pr)
    {
        return $this->withOption(RiakOption::PR, $pr);
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
