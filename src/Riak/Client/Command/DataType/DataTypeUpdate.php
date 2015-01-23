<?php

namespace Riak\Client\Command\DataType;

/**
 * An object that represents an update to a Riak datatype.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface DataTypeUpdate
{
    /**
     * @return \Riak\Client\Core\Query\Crdt\Op\CrdtOp
     */
    public function getOp();
}
