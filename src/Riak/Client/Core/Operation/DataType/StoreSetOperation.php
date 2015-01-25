<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Command\DataType\Response\StoreSetResponse;
use Riak\Client\Core\Query\Crdt\DataType;

/**
 * An operation used to store a set in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSetOperation extends StoreDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null, $context = null)
    {
        return new StoreSetResponse($this->location, $datatype, $context);
    }
}
