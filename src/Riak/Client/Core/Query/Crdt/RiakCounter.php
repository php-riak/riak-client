<?php

namespace Riak\Client\Core\Query\Crdt;

/**
 * Representation of the Riak counter datatype.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakCounter implements DataType
{
    /**
     * @var integer
     */
    private $value;

    /**
     * @param integer $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }
}
