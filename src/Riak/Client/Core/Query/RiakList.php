<?php

namespace Riak\Client\Core\Query;

use OutOfBoundsException;
use IteratorAggregate;
use ArrayIterator;
use ArrayAccess;
use Countable;

/**
 * Interface for riak object lists.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class RiakList implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @var array
     */
    protected $list;

    /**
     * @param array $list
     */
    public function __construct(array $list = [])
    {
        $this->list = $list;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        return reset($this->list) ?: null;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->list);
    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->list);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new ArrayIterator($this->list);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->list[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        if ( ! isset($this->list[$offset])) {
            throw new OutOfBoundsException("Undefined key : $offset");
        }

        return $this->list[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (isset($offset)) {
            return $this->list[$offset] = $value;
        }

        return $this->list[] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        if ( ! isset($this->list[$offset])) {
            throw new OutOfBoundsException("Undefined key : $offset");
        }

        unset($this->list[$offset]);
    }
}
