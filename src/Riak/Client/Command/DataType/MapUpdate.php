<?php

namespace Riak\Client\Command\DataType;

use InvalidArgumentException;
use Riak\Client\Core\Query\Crdt\Op\CrdtOp;
use Riak\Client\Core\Query\Crdt\Op\SetOp;
use Riak\Client\Core\Query\Crdt\Op\MapOp;
use Riak\Client\Core\Query\Crdt\Op\FlagOp;
use Riak\Client\Core\Query\Crdt\Op\CounterOp;
use Riak\Client\Core\Query\Crdt\Op\RegisterOp;

/**
 * An update to a Riak map datatype.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MapUpdate implements DataTypeUpdate
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
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function removeCounter($key)
    {
        $this->reomoveCrdt($key, 'counter');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function removeRegister($key)
    {
        $this->reomoveCrdt($key, 'register');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function removeFlag($key)
    {
        $this->reomoveCrdt($key, 'flag');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function removeSet($key)
    {
        $this->reomoveCrdt($key, 'set');

        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Riak\Client\Command\DataType\StoreMap
     */
    public function removeMap($key)
    {
        $this->reomoveCrdt($key, 'map');

        return $this;
    }

    /**
     * @param string                                $key
     * @param \Riak\Client\Core\Query\Crdt\Op\SetOp $op
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function updateSet($key, SetOp $op)
    {
        return $this->updateCrdt($key, 'set', $op);
    }

    /**
     * @param string                                    $key
     * @param \Riak\Client\Core\Query\Crdt\Op\CounterOp $op
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function updateCounter($key, CounterOp $op)
    {
        return $this->updateCrdt($key, 'counter', $op);
    }

    /**
     * @param string                                  $key
     * @param \Riak\Client\Command\DataType\MapUpdate $op
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function updateMap($key, MapOp $op)
    {
        return $this->updateCrdt($key, 'map', $op);
    }

    /**
     * @param string                                     $key
     * @param \Riak\Client\Core\Query\Crdt\Op\RegisterOp $op
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function updateRegister($key, RegisterOp $op)
    {
        return $this->updateCrdt($key, 'register', $op);
    }

    /**
     * @param string                                 $key
     * @param \Riak\Client\Core\Query\Crdt\Op\FlagOp $op
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public function updateFlag($key, FlagOp $op)
    {
        return $this->updateCrdt($key, 'flag', $op);
    }

    /**
     * @param string                                 $key
     * @param string                                 $type
     * @param \Riak\Client\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    private function updateCrdt($key, $type, CrdtOp $op)
    {
        $this->updates[$type][$key] = $op;

        return $this;
    }

    /**
     * @param string $key
     * @param string $type
     *
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    private function reomoveCrdt($key, $type)
    {
        $this->removes[$type][$key] = $key;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOp()
    {
        return new MapOp($this->updates, $this->removes);
    }

    /**
     * @return \Riak\Client\Command\DataType\MapUpdate
     */
    public static function create()
    {
        return new MapUpdate();
    }

    /**
     * @param array $updates
     *
     * @return \Riak\Client\Command\DataType\SetUpdate
     */
    public static function createFromArray(array $updates)
    {
        $update = new MapUpdate();

        foreach ($updates as $key => $val) {

            if (is_bool($val)) {
                $update->updateFlag($key, new FlagOp($val));

                continue;
            }

            if (is_string($val)) {
                $update->updateRegister($key, new RegisterOp($val));

                continue;
            }

            if (is_int($val)) {
                $update->updateCounter($key, new CounterOp($val));

                continue;
            }

            if (is_array($val) && ($val === array_values($val))) {

                $update->updateSet($key, new SetOp($val, []));

                continue;
            }

            if (is_array($val)) {

                $update->updateMap($key, self::createFromArray($val)->getOp());

                continue;
            }

            $message = 'Map element "%s" must be of the type (boolean, string, integer, or an array), "%s" given.';
            $type    = is_object($val) ? get_class($val) : gettype($val);

            throw new InvalidArgumentException(sprintf($message, $key, $type));
        }

        return $update;
    }
}
