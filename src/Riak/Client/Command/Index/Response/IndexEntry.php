<?php

namespace Riak\Client\Command\Index\Response;

use Riak\Client\Core\Query\RiakLocation;

/**
 * Riak 2i index query entry.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexEntry
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var mixed
     */
    private $indexKey;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param mixed                                $indexKey
     */
    public function __construct(RiakLocation $location, $indexKey)
    {
        $this->location = $location;
        $this->indexKey = $indexKey;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return mixed
     */
    public function getIndexKey()
    {
        return $this->indexKey;
    }
}
