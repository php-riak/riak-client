<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\Core\Query\Crdt\Op\CounterOp;

/**
 * An update to a Riak counter datatype.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CounterUpdate implements DataTypeUpdate
{
    /**
     * @var integer
     */
    private $delta;

    /**
     * @param integer $delta
     */
    public function __construct($delta = 0)
    {
        $this->delta = $delta;
    }

    /**
     * @param integer $delta
     *
     * @return \Riak\Client\Command\DataType\CounterUpdate
     */
    public function withDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOp()
    {
        return new CounterOp($this->delta);
    }
}
