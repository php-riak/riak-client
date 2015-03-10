<?php

namespace Riak\Client\Command\MapReduce\Response;

use Iterator;
use Riak\Client\Core\RiakIterator;

/**
 * A Riak MapReduce reponse iterator.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MapReduceEntryIterator implements Iterator
{
    /**
     * @var \Riak\Client\Core\RiakContinuableIterator
     */
    private $iterator;

    /**
     * @var \Riak\Client\Command\MapReduce\Response\MapReduceEntry
     */
    private $current;

    /**
     * @param \Riak\Client\Core\RiakIterator $iterator
     */
    public function __construct(RiakIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function readNext()
    {
        $item = $this->iterator->current();

        if ($item == null) {
            return null;
        }

        return new MapReduceEntry($item->phase, $item->response);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();

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
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        $this->current = $this->readNext();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return $this->iterator->valid();
    }
}
