<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Command\DataType\StoreMap;
use Riak\Client\Core\Query\Crdt\Op\FlagOp;
use Riak\Client\Command\DataType\SetUpdate;
use Riak\Client\Command\DataType\MapUpdate;
use Riak\Client\Core\Query\Crdt\Op\CounterOp;
use Riak\Client\Core\Query\Crdt\Op\RegisterOp;

/**
 * Used to construct a StoreMap command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreMapBuilder extends StoreDataTypeBuilder
{
    /**
     * @var \Riak\Client\Command\DataType\MapUpdate
     */
    protected $update;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location = null, array $options = array())
    {
        parent::__construct($location, $options);

        $this->update = new MapUpdate();
    }

    /**
     * Update the map in Riak by removing the counter mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function removeCounter($key)
    {
        $this->update->removeCounter($key);

        return $this;
    }

    /**
     * Update the map in Riak by removing the register mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function removeRegister($key)
    {
        $this->update->removeRegister($key);

        return $this;
    }

    /**
     * Update the map in Riak by removing the flag mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function removeFlag($key)
    {
        $this->update->removeFlag($key);

        return $this;
    }

    /**
     * Update the map in Riak by removing the set mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function removeSet($key)
    {
        $this->update->removeSet($key);

        return $this;
    }

    /**
     * Update the map in Riak by removing the map mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function removeMap($key)
    {
        $this->update->removeMap($key);

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the map mapped to the provided key.
     *
     * @param string                                        $key
     * @param \Riak\Client\Command\DataType\MapUpdate|array $value
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function updateMap($key, $value)
    {
        $update = ( ! $value instanceof MapUpdate)
            ? MapUpdate::createFromArray($value)
            : $value;

        $this->update->updateMap($key, $update->getOp());

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the set mapped to the provided key.
     *
     * @param string                                        $key
     * @param \Riak\Client\Command\DataType\SetUpdate|array $value
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function updateSet($key, $value)
    {
        $update = ( ! $value instanceof SetUpdate)
            ? SetUpdate::createFromArray($value)
            : $value;

        $this->update->updateSet($key, $update->getOp());

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the counter mapped to the provided key.
     *
     * @param string  $key
     * @param integer $value
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function updateCounter($key, $value)
    {
        $this->update->updateCounter($key, new CounterOp($value));

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the register mapped to the provided key.
     *
     * @param string $key
     * @param string $value
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function updateRegister($key, $value)
    {
        $this->update->updateRegister($key, new RegisterOp($value));

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the flag mapped to the provided key.
     *
     * @param string  $key
     * @param boolean $value
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function updateFlag($key, $value)
    {
        $this->update->updateFlag($key, new FlagOp($value));

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function build()
    {
        return new StoreMap($this->location, $this->update, $this->context, $this->options);
    }
}
