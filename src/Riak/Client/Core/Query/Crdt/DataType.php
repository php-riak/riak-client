<?php

namespace Riak\Client\Core\Query\Crdt;

/**
 * Base classe for all datatype.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface DataType
{
    /**
     * @return mixed
     */
    public function getValue();
}
