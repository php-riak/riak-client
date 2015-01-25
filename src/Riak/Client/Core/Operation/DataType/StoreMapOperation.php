<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Command\DataType\Response\StoreMapResponse;
use Riak\Client\Core\Query\Crdt\DataType;

/**
 * An operation used to store a map in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreMapOperation extends StoreDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null, $context = null)
    {
        return new StoreMapResponse($this->location, $datatype, $context);
    }
}
