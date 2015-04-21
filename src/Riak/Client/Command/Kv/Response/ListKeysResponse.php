<?php

namespace Riak\Client\Command\Kv\Response;

use Iterator;

/**
 * List Keys Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListKeysResponse extends Response
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $locations;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakLocation[]
     */
    public function getLocations()
    {
        if ($this->locations === null) {
            $this->locations = iterator_to_array($this->iterator);
        }

        return $this->locations;
    }
}
