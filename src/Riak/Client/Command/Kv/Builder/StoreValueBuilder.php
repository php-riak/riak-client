<?php

namespace Riak\Client\Command\Kv\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\RiakOption;

/**
 * Used to construct a StoreValue command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreValueBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Riak\Client\Core\Query\RiakObject|mixed
     */
    private $value;

    /**
     * @param \Riak\Client\Command\Kv\RiakLocation     $location
     * @param \Riak\Client\Core\Query\RiakObject|mixed $value
     */
    public function __construct(RiakLocation $location = null, $value = null)
    {
        $this->location = $location;
        $this->value    = $value;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withLocation(RiakLocation $location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakObject|mixed $value
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set the pw value.
     *
     * @param integer $pw
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withPw($pw)
    {
        return $this->withOption(RiakOption::PW, $pw);
    }

    /**
     * Set the dw value.
     *
     * @param integer $dw
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
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
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withW($w)
    {
        return $this->withOption(RiakOption::W, $w);
    }

    /**
     * Set the returnbody value.
     *
     * @param integer $flag
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withReturnBody($flag)
    {
        return $this->withOption(RiakOption::RETURN_BODY, $flag);
    }

    /**
     * Build a FetchValue object
     *
     * @return \Riak\Client\Command\Kv\StoreValue
     */
    public function build()
    {
        return new StoreValue($this->location, $this->value, $this->options);
    }
}
