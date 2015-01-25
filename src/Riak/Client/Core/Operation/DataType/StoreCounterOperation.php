<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Command\DataType\Response\StoreCounterResponse;
use Riak\Client\Core\Query\Crdt\DataType;

/**
 * An operation used to store a counter from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreCounterOperation extends StoreDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null, $context = null)
    {
        return new StoreCounterResponse($this->location, $datatype, $context);
    }
}
