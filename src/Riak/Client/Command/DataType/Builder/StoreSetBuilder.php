<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\StoreSet;
use Riak\Client\Command\DataType\SetUpdate;

/**
 * Used to construct a StoreSet command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSetBuilder extends StoreDataTypeBuilder
{
    /**
     * @var \Riak\Client\Command\DataType\SetUpdate
     */
    protected $update;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location = null, array $options = array())
    {
        parent::__construct($location, $options);

        $this->update = new SetUpdate();
    }

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function add($value)
    {
        $this->update->add($value);

        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function remove($value)
    {
        $this->update->remove($value);

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\StoreSet
     */
    public function build()
    {
        return new StoreSet($this->location, $this->update, $this->context, $this->options);
    }
}
