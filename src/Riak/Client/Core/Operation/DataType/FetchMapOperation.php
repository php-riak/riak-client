<?php

namespace Riak\Client\Core\Operation\DataType;

use Riak\Client\Command\DataType\Response\FetchMapResponse;
use Riak\Client\Core\Query\Crdt\DataType;

/**
 * An operation used to fetch a map from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchMapOperation extends FetchDataTypeOperation
{
    /**
     * {@inheritdoc}
     */
    public function createDataTypeResponse(DataType $datatype = null, $context = null)
    {
        return new FetchMapResponse($this->location, $datatype, $context);
    }
}
