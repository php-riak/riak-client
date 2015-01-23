<?php

namespace Riak\Client\Core\Query\Crdt\Op;

/**
 * Riak Register crdt op.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RegisterOp implements CrdtOp
{
    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
