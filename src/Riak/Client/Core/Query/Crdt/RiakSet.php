<?php

namespace Riak\Client\Core\Query\Crdt;

/**
 * Representation of the Riak set datatype.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakSet implements DataType
{
    /**
     * @var array
     */
    private $elements;

    /**
     * @param array $elements
     */
    public function __construct(array $elements)
    {
        $this->elements = $elements;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->elements;
    }
}
