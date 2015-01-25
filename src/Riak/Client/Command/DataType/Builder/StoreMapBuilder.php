<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\Command\DataType\StoreMap;

/**
 * Used to construct a StoreMap command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreMapBuilder extends Builder
{
    /**
     * @var array
     */
    private $removes = [
        'register'  => [],
        'counter'   => [],
        'flag'      => [],
        'map'       => [],
        'set'       => [],
    ];

    /**
     * @var array
     */
    private $updates = [
        'register'  => [],
        'counter'   => [],
        'flag'      => [],
        'map'       => [],
        'set'       => [],
    ];

    /**
     * Update the map in Riak by removing the counter mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function removeCounter($key)
    {
        $this->removes['counter'][] = $key;

        return $this;
    }

    /**
     * Update the map in Riak by removing the register mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function removeRegister($key)
    {
        $this->removes['register'][] = $key;

        return $this;
    }

    /**
     * Update the map in Riak by removing the flag mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function removeFlag($key)
    {
        $this->removes['flag'][] = $key;

        return $this;
    }

    /**
     * Update the map in Riak by removing the set mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function removeSet($key)
    {
        $this->removes['set'][] = $key;

        return $this;
    }

    /**
     * Update the map in Riak by removing the map mapped to the provided key.
     *
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function removeMap($key)
    {
        $this->removes['map'][] = $key;

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the map mapped to the provided key.
     *
     * @param string                                        $key
     * @param \Riak\Client\Command\DataType\MapUpdate|array $value
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function updateMap($key, $value)
    {
        $this->updates['map'][$key] = $value;

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the set mapped to the provided key.
     *
     * @param string                                        $key
     * @param \Riak\Client\Command\DataType\SetUpdate|array $value
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function updateSet($key, $value)
    {
        $this->updates['set'][$key] = $value;

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the counter mapped to the provided key.
     *
     * @param string  $key
     * @param integer $value
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function updateCounter($key, $value)
    {
        $this->updates['counter'][$key] = $value;

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the register mapped to the provided key.
     *
     * @param string $key
     * @param string $value
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function updateRegister($key, $value)
    {
        $this->updates['register'][$key] = $value;

        return $this;
    }

    /**
     * Update the map in Riak by adding/updating the flag mapped to the provided key.
     *
     * @param string  $key
     * @param boolean $value
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function updateFlag($key, $value)
    {
        $this->updates['flag'][$key] = $value;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\Builder\StoreMapBuilder
     */
    public function build()
    {
        $command = new StoreMap($this->location, $this->options);

        if ($this->context != null) {
            $command->withContext($this->context);
        }

        array_map([$command, 'updateRegister'], array_keys($this->updates['register']), $this->updates['register']);
        array_map([$command, 'updateCounter'], array_keys($this->updates['counter']), $this->updates['counter']);
        array_map([$command, 'updateFlag'], array_keys($this->updates['flag']), $this->updates['flag']);
        array_map([$command, 'updateMap'], array_keys($this->updates['map']), $this->updates['map']);
        array_map([$command, 'updateSet'], array_keys($this->updates['set']), $this->updates['set']);

        array_map([$command, 'removeRegister'], $this->removes['register']);
        array_map([$command, 'removecounter'], $this->removes['counter']);
        array_map([$command, 'removeFlag'], $this->removes['flag']);
        array_map([$command, 'removeMap'], $this->removes['map']);
        array_map([$command, 'removeSet'], $this->removes['set']);

        return $command;
    }
}
