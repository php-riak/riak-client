<?php

namespace Riak\Client\Core\Transport\Proto\DataType;

use Protobuf\Stream;
use Riak\Client\ProtoBuf;
use InvalidArgumentException;
use Riak\Client\Core\Query\Crdt\Op;
use Riak\Client\ProtoBuf\MapField\MapFieldType;

/**
 * Crdt Op Converter
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CrdtOpConverter
{
    /**
     * @param \Riak\Client\ProtoBuf\MapEntry[] $entries
     *
     * @return array
     */
    public function fromProtoBuf($entries)
    {
        $values = [];

        foreach ($entries as $entry) {
            $field = $entry->getField();
            $name  = (string) $field->getName();
            $value = $this->convertMapEntry($entry);

            $values[$name] = $value;
        }

        return $values;
    }

    /**
     * @param \Riak\Client\ProtoBuf\MapEntry[] $entry
     *
     * @return mixed
     */
    public function convertMapEntry(ProtoBuf\MapEntry $entry)
    {
        $field = $entry->getField();
        $type  = $field->getType();

        if ($type === MapFieldType::MAP()) {
            return $this->fromProtoBuf($entry->getMapValueList());
        }

        if ($type === MapFieldType::SET()) {
            $set = [];

            foreach ($entry->getSetValueList() as $val) {
                $set[]  = $val->getContents();
            }

            return $set;
        }

        if ($type === MapFieldType::FLAG()) {
            return ($entry->getFlagValue() == ProtoBuf\MapUpdate\FlagOp::ENABLE());
        }

        if ($type === MapFieldType::COUNTER()) {
            return $entry->getCounterValue();
        }

        if ($type === MapFieldType::REGISTER()) {
            return $entry->getRegisterValue()->getContents();
        }

        throw new InvalidArgumentException(sprintf('Unknown crdt field type : %s', $type));
    }

    /**
     * @param \Riak\Client\ProtoBuf\DtOp $op
     *
     * @return \Riak\Client\ProtoBuf\DtOp
     */
    public function toProtoBuf(Op\CrdtOp $op)
    {
        $crdtOp = new ProtoBuf\DtOp();

        if ($op instanceof Op\CounterOp) {
            $crdtOp->setCounterOp($this->convertCounter($op));

            return $crdtOp;
        }

        if ($op instanceof Op\SetOp) {
            $crdtOp->setSetOp($this->convertSet($op));

            return $crdtOp;
        }

        if ($op instanceof Op\MapOp) {
            $crdtOp->setMapOp($this->convertMap($op));

            return $crdtOp;
        }

        throw new InvalidArgumentException(sprintf('Unknown data type op : %s', get_class($op)));
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\CounterOp $op
     *
     * @return \Riak\Client\ProtoBuf\CounterOp
     */
    protected function convertCounter(Op\CounterOp $op)
    {
        $counterOp = new ProtoBuf\CounterOp();
        $increment = $op->getIncrement();

        $counterOp->setIncrement($increment);

        return $counterOp;
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\SetOp $op
     *
     * @return \Riak\Client\ProtoBuf\SetOp
     */
    protected function convertSet(Op\SetOp $op)
    {
        $setOp = new ProtoBuf\SetOp();

        foreach ($op->getRemoves() as $value) {
            $setOp->addRemoves((string) $value);
        }

        foreach ($op->getAdds() as $value) {
            $setOp->addAdds((string) $value);
        }

        return $setOp;
    }

    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\FlagOp $op
     *
     * @return integer
     */
    protected function convertFlag(Op\FlagOp $op)
    {
        return $op->isEnabled()
            ? ProtoBuf\MapUpdate\FlagOp::ENABLE()
            : ProtoBuf\MapUpdate\FlagOp::DISABLE();
    }
    /**
     * @param \Riak\Client\Core\Query\Crdt\Op\MapOp $op
     *
     * @return \Riak\Client\ProtoBuf\MapOp
     */
    protected function convertMap(Op\MapOp $op)
    {
        $mapOp = new ProtoBuf\MapOp();

        foreach ($op->getMapUpdates() as $key => $value) {
            $map    = $this->convertMap($value);
            $update = $this->createMapUpdate($key, MapFieldType::MAP(), $map);

            $mapOp->addUpdates($update);
        }

        foreach ($op->getSetUpdates() as $key => $value) {
            $set    = $this->convertSet($value);
            $update = $this->createMapUpdate($key, MapFieldType::SET(), $set);

            $mapOp->addUpdates($update);
        }

        foreach ($op->getFlagUpdates() as $key => $value) {
            $flag   = $this->convertFlag($value);
            $update = $this->createMapUpdate($key, MapFieldType::FLAG(), $flag);

            $mapOp->addUpdates($update);
        }

        foreach ($op->getCounterUpdates() as $key => $value) {
            $counter = $this->convertCounter($value);
            $update  = $this->createMapUpdate($key, MapFieldType::COUNTER(), $counter);

            $mapOp->addUpdates($update);
        }

        foreach ($op->getRegisterUpdates() as $key => $value) {
            $register = $value->getValue();
            $update   = $this->createMapUpdate($key, MapFieldType::REGISTER(), $register);

            $mapOp->addUpdates($update);
        }

        foreach ($op->getMapRemoves() as $key => $value) {
            $mapOp->addRemoves($this->createMapField($key, MapFieldType::MAP()));
        }

        foreach ($op->getSetRemoves() as $key => $value) {
            $mapOp->addRemoves($this->createMapField($key, MapFieldType::SET()));
        }

        foreach ($op->getFlagRemoves() as $key => $value) {
            $mapOp->addRemoves($this->createMapField($key, MapFieldType::FLAG()));
        }

        foreach ($op->getCounterRemoves() as $key => $value) {
            $mapOp->addRemoves($this->createMapField($key, MapFieldType::COUNTER()));
        }

        foreach ($op->getRegisterRemoves() as $key => $value) {
            $mapOp->addRemoves($this->createMapField($key, MapFieldType::REGISTER()));
        }

        return $mapOp;
    }

    /**
     * @param string    $fieldName
     * @param integer   $fieldType
     * @param mixed     $value
     *
     * @return \Riak\Client\ProtoBuf\MapUpdate
     */
    protected function createMapUpdate($fieldName, $fieldType, $value)
    {
        $update    = new ProtoBuf\MapUpdate();
        $field     = $this->createMapField($fieldName, $fieldType);

        $update->setField($field);

        if ($fieldType === MapFieldType::MAP()) {
            $update->setMapOp($value);
        }

        if ($fieldType === MapFieldType::SET()) {
            $update->setSetOp($value);
        }

        if ($fieldType === MapFieldType::FLAG()) {
            $update->setFlagOp($value);
        }

        if ($fieldType === MapFieldType::COUNTER()) {
            $update->setCounterOp($value);
        }

        if ($fieldType === MapFieldType::REGISTER()) {
            $update->setRegisterOp($value);
        }

        return $update;
    }

    /**
     * @param string       $fieldName
     * @param MapFieldType $fieldType
     *
     * @return \Riak\Client\ProtoBuf\MapField
     */
    protected function createMapField($fieldName, MapFieldType $fieldType)
    {
        $field = new ProtoBuf\MapField();

        $field->setName($fieldName);
        $field->setType($fieldType);

        return $field;
    }
}
