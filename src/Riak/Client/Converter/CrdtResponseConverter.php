<?php

namespace Riak\Client\Converter;

use Riak\Client\Core\Message\DataType\Response;
use Riak\Client\Core\Query\Crdt\RiakCounter;
use Riak\Client\Core\Query\Crdt\RiakMap;
use Riak\Client\Core\Query\Crdt\RiakSet;
use InvalidArgumentException;

/**
 * Crdt response converter
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class CrdtResponseConverter
{
    /**
     * @param \Riak\Client\Core\Message\DataType\Response $response
     *
     * @return \Riak\Client\Core\Query\Crdt\DataType;
     */
    public function convert(Response $response)
    {
        if ($response->type == null) {
            return;
        }

        if ($response->type == 'counter') {
            return $this->convertCounter($response);
        }

        if ($response->type == 'set') {
            return $this->convertSet($response);
        }

        if ($response->type == 'map') {
            return $this->convertMap($response);
        }

        throw new InvalidArgumentException("Unknown crdt type : {$response->type}");
    }

    /**
     * @param \Riak\Client\Core\Message\DataType\Response $response
     *
     * @return \Riak\Client\Core\Query\Crdt\RiakCounter
     */
    public function convertCounter(Response $response)
    {
        $value   = $response->value;
        $counter = new RiakCounter($value);

        return $counter;
    }

    /**
     * @param \Riak\Client\Core\Message\DataType\Response $response
     *
     * @return \Riak\Client\Core\Query\Crdt\RiakSet
     */
    public function convertSet(Response $response)
    {
        $value = $response->value;
        $set   = new RiakSet($value);

        return $set;
    }

    /**
     * @param \Riak\Client\Core\Message\DataType\Response $response
     *
     * @return \Riak\Client\Core\Query\Crdt\RiakMap
     */
    public function convertMap(Response $response)
    {
        $value = $response->value;
        $map   = new RiakMap($value);

        return $map;
    }
}
