<?php

namespace Riak\Client\Core\Query\Crdt\Op;

/**
 * Riak Counter crdt op.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CounterOp implements CrdtOp
{
    /**
     * @var integer
     */
    private $increment;

    /**
     * @param integer $increment
     */
    public function __construct($increment)
    {
        $this->increment = $increment;
    }

    /**
     * @return integer
     */
    public function getIncrement()
    {
        return $this->increment;
    }
}
