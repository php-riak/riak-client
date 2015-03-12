<?php

namespace Riak\Client\Core;

use Iterator;

/**
 * Riak transport iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class RiakIterator implements Iterator
{
    /**
     * @var integer
     */
    protected $count = 0;

    /**
     * @var mixed
     */
    protected $current;

    /**
     * @return mixed
     */
    abstract protected function readNext();

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->count   = $this->count + 1;
        $this->current = $this->readNext();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->count   = 0;
        $this->current = $this->readNext();
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return ($this->current !== null);
    }
}
