<?php

namespace Riak\Client\Core\Transport\Http\DataType;

use InvalidArgumentException;
use Riak\Client\Core\Query\Crdt\Op\MapOp;
use Riak\Client\Core\Query\Crdt\Op\SetOp;
use Riak\Client\Core\Query\Crdt\Op\CrdtOp;
use Riak\Client\Core\Query\Crdt\Op\FlagOp;
use Riak\Client\Core\Query\Crdt\Op\CounterOp;
use Riak\Client\Core\Query\Crdt\Op\RegisterOp;

/**
 * Crdt Op Converter
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CrdtOpConverter
{
    /**
     * @param string $type
     * @param mixed  $value
     *
     * @return mixed
     */
    public function fromArray($type, $value)
    {
        if ($type !== 'map') {
            return $value;
        }

        $data = [];

        foreach ($value as $key => $val) {
            if (substr($key, -7) === 'counter') {
                $data[substr($key, 0, -8)] = $val;
            }

            if (substr($key, -8) === 'register') {
                $data[substr($key, 0, -9)] = $val;
            }

            if (substr($key, -4) === 'flag') {
                $data[substr($key, 0, -5)] = $val;
            }

            if (substr($key, -3) === 'set') {
                $data[substr($key, 0, -4)] = $val;
            }

            if (substr($key, -3) === 'map') {
                $data[substr($key, 0, -4)] = $this->fromArray('map', $val);
            }
        }

        return $data;
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\CrdtOp $op
     *
     * @return string
     */
    public function convert(CrdtOp $op)
    {
        if ($op instanceof CounterOp) {
            return $this->convertCounter($op);
        }

        if ($op instanceof SetOp) {
            return $this->convertSet($op);
        }

        if ($op instanceof MapOp) {
            return $this->convertMap($op);
        }

        throw new InvalidArgumentException(sprintf('Unknown crdt op : %s', get_class($op)));
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\RegisterOp $op
     *
     * @return string
     */
    private function convertRegister(RegisterOp $op)
    {
         return $op->getValue();
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\CounterOp $op
     *
     * @return integer
     */
    private function convertCounter(CounterOp $op)
    {
        return $op->getIncrement();
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\FlagOp $op
     *
     * @return string
     */
    private function convertFlag(FlagOp $op)
    {
         return $op->isEnabled()
            ? 'enable'
            : 'disable';
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\SetOp $op
     *
     * @return array
     */
    private function convertSet(SetOp $op)
    {
        $value  = [];
        $add    = $op->getAdds();
        $remove = $op->getRemoves();

        if ( ! empty($add)) {
            $value['add_all'] = $add;
        }

        if ( ! empty($remove)) {
            //$remove is always array so we need remove_all
            $value['remove_all'] = $remove;
        }

        return $value;
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\MapOp $op
     *
     * @return array
     */
    private function convertMap(MapOp $op)
    {
        $updates = [];
        $removes = [];
        $values  = [];

        foreach ($op->getMapUpdates() as $key => $value) {
            $updates["{$key}_map"] = $this->convertMap($value);
        }

        foreach ($op->getSetUpdates() as $key => $value) {
            $updates["{$key}_set"] = $this->convertSet($value);
        }

        foreach ($op->getFlagUpdates() as $key => $value) {
            $updates["{$key}_flag"] = $this->convertFlag($value);
        }

        foreach ($op->getCounterUpdates() as $key => $value) {
            $updates["{$key}_counter"] = $this->convertCounter($value);
        }

        foreach ($op->getRegisterUpdates() as $key => $value) {
            $updates["{$key}_register"] = $this->convertRegister($value);
        }

        foreach ($op->getMapRemoves() as $key => $value) {
            $removes["{$key}_map"] = $key;
        }

        foreach ($op->getSetRemoves() as $key => $value) {
            $removes["{$key}_set"] = $key;
        }

        foreach ($op->getFlagRemoves() as $key => $value) {
            $removes["{$key}_flag"] = $key;
        }

        foreach ($op->getCounterRemoves() as $key => $value) {
            $removes["{$key}_counter"] = $key;
        }

        foreach ($op->getRegisterRemoves() as $key => $value) {
            $removes["{$key}_register"] = $key;
        }

        if ( ! empty($updates)) {
            $values['update'] = $updates;
        }

        if ( ! empty($removes)) {
            $values['remove'] = array_keys($removes);
        }

        return $values;
    }
}
