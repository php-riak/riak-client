<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\Query\Crdt\Op\SetOp;

/**
 * An update to a Riak set datatype.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SetUpdate implements DataTypeUpdate
{
    /**
     * @var array
     */
    private $adds = [];

    /**
     * @var array
     */
    private $removes = [];

    /**
     * Add the provided value to the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\SetUpdate
     */
    public function add($value)
    {
        $this->adds[] = $value;

        return $this;
    }

    /**
     * Remove the provided value from the set in Riak.
     *
     * @param mixed $value
     *
     * @return \Riak\Client\Command\DataType\SetUpdate
     */
    public function remove($value)
    {
        $this->removes[] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOp()
    {
        return new SetOp($this->adds, $this->removes);
    }

    /**
     * @return \Riak\Client\Command\DataType\SetUpdate
     */
    public static function create()
    {
        return new SetUpdate();
    }

    /**
     * @param array $adds
     *
     * @return \Riak\Client\Command\DataType\SetUpdate
     */
    public static function createFromArray(array $adds)
    {
        $update = new SetUpdate();

        array_walk($adds, [$update, 'add']);

        return $update;
    }
}
