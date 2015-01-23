<?php

namespace Riak\Client\Command\Kv\Response;

use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakObjectList;
use Riak\Client\Converter\ConverterFactory;

/**
 * Store Value Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreValueResponse extends Response
{
    /**
     * @var string
     */
    private $generatedKey;

    /**
     * @return string
     */
    public function getGeneratedKey()
    {
        return $this->unchanged;
    }

    /**
     * @param string $generatedKey
     */
    public function setGeneratedKey($generatedKey)
    {
        $this->generatedKey = $generatedKey;
    }
}
