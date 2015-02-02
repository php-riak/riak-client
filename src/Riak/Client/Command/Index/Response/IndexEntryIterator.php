<?php

namespace Riak\Client\Command\Index\Response;

use Iterator;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Index\Response\IndexEntry;

/**
 * A Riak 2i index reponse iterator.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexEntryIterator implements Iterator
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @var \Iterator
     */
    private $innerIterator;

    /**
     * @var \Riak\Client\Command\Index\Response\IndexEntry
     */
    private $current;

    /**
     * @var integer
     */
    private $count;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param \Iterator                             $iterator
     */
    public function __construct(RiakNamespace $namespace, Iterator $iterator)
    {
        $this->namespace = $namespace;
        $this->iterator  = $iterator;
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
    public function key()
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->innerIterator->next();

        $this->count   = $this->count + 1;
        $this->current = $this->readNext();
    }

    /**
     * @return \Riak\Client\Command\Index\Response\IndexEntry
     */
    public function readNext()
    {
        if ($this->innerIterator == null) {
            return null;
        }

        if ($this->innerIterator->valid()) {
            return $this->currentEntry();
        }

        $this->iterator->next();

        $this->innerIterator = $this->iterator->valid()
            ? $this->iterator->current()
            : null;

        if ($this->innerIterator == null) {
            return null;
        }

        $this->innerIterator->rewind();

        return $this->currentEntry();
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

        $this->current = $this->readNext();
        $this->count   = 0;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return ($this->current !== null);
    }

    /**
     * @return \Riak\Client\Command\Index\Response\IndexEntry
     */
    public function currentEntry()
    {
        if ( ! $this->innerIterator || ! $this->innerIterator->valid()) {
            return null;
        }

        $entry    = $this->innerIterator->current();
        $location = new RiakLocation($this->namespace, $entry->objectKey);
        $current  = new IndexEntry($location, $entry->indexKey);

        return $current;
    }
}
