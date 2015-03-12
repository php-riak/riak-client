<?php

namespace Riak\Client\Core;

use Iterator;
use Riak\Client\Core\RiakIterator;

/**
 * Iterate over each element of an iterator.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class RiakEntryIterator extends RiakIterator
{
    /**
     * @var \Iterator
     */
    protected $iterator;

    /**
     * @var \Iterator
     */
    protected $innerIterator;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->innerIterator->next();

        parent::next();
    }

    /**
     * @return \Riak\Client\Command\Index\Response\IndexEntry
     */
    protected function readNext()
    {
        if ($this->innerIterator == null) {
            return null;
        }

        if ($this->innerIterator->valid()) {
            return $this->readNextInnerEntry();
        }

        $this->iterator->next();

        $this->innerIterator = $this->iterator->valid()
            ? $this->iterator->current()
            : null;

        if ($this->innerIterator == null) {
            return null;
        }

        $this->innerIterator->rewind();

        return $this->readNextInnerEntry();
    }

    /**
     * @return mixed
     */
    protected function readNextInnerEntry()
    {
        if ( ! $this->innerIterator || ! $this->innerIterator->valid()) {
            return null;
        }

        return $this->currentInnerEntry();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        $this->innerIterator = $this->iterator->valid()
            ? $this->iterator->current()
            : null;

        parent::rewind();
    }

    /**
     * @return mixed
     */
    abstract protected function currentInnerEntry();
}
