<?php

namespace Riak\Client\Core\Query\Meta;

use Riak\Client\Core\Query\RiakList;

/**
 * Represents list of riak metas.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakUsermeta extends RiakList
{
    /**
     * Set a user metadata entry.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function put($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Get a user metadata entry.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Removes a user metadata entry.
     *
     * @param string $key
     */
    public function remove($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->list;
    }
}
