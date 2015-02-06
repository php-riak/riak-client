<?php

namespace Riak\Client\Command\Index\Response;

use Riak\Client\Core\RiakIterator;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\RiakContinuableIterator;
use Riak\Client\Command\Index\Response\IndexEntry;

/**
 * A Riak 2i index reponse iterator.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexEntryIterator extends RiakIterator implements RiakContinuableIterator
{
    /**
     * @var \Riak\Client\Core\RiakContinuableIterator
     */
    private $iterator;

    /**
     * @var \Iterator
     */
    private $innerIterator;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace                        $namespace
     * @param \Riak\Client\Core\Transport\RiakTransportContinuableIterator $iterator
     */
    public function __construct(RiakNamespace $namespace, RiakContinuableIterator $iterator)
    {
        $this->namespace = $namespace;
        $this->iterator  = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function hasContinuation()
    {
        return $this->iterator->hasContinuation();
    }

    /**
     * {@inheritdoc}
     */
    public function getContinuation()
    {
        return $this->iterator->getContinuation();
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

        parent::rewind();
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
