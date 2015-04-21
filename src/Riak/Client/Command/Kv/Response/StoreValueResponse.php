<?php

namespace Riak\Client\Command\Kv\Response;

/**
 * Store Value Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreValueResponse extends ObjectResponse
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
        return $this->generatedKey;
    }

    /**
     * @param string $generatedKey
     */
    public function setGeneratedKey($generatedKey)
    {
        $this->generatedKey = $generatedKey;
    }
}
