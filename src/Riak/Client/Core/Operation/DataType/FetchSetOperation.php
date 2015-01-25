<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Command\DataType\Response\FetchSetResponse;
use Riak\Client\Core\Query\Crdt\DataType;

/**
 * An operation used to fetch a set from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSetOperation extends FetchDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null, $context = null)
    {
        return new FetchSetResponse($this->location, $datatype, $context);
    }
}
