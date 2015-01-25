<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Command\DataType\Response\FetchCounterResponse;
use Riak\Client\Core\Query\Crdt\DataType;

/**
 * An operation used to fetch a counter from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchCounterOperation extends FetchDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null, $context = null)
    {
        return new FetchCounterResponse($this->location, $datatype, $context);
    }
}
