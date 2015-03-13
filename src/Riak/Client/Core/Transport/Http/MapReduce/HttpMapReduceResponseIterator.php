<?php

namespace Riak\Client\Core\Transport\Http\MapReduce;

use Riak\Client\Core\RiakIterator;
use Riak\Client\Core\Message\MapReduce\MapReduceEntry;
use Riak\Client\Core\Transport\Http\MultipartResponseIterator;

/**
 * Http index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpMapReduceResponseIterator extends RiakIterator
{
    /**
     * @var \Riak\Client\Core\Transport\Http\MultipartResponseIterator
     */
    private $iterator;

    /**
     * @param \Riak\Client\Core\Transport\Http\MultipartResponseIterator $iterator
     */
    public function __construct(MultipartResponseIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function readNext()
    {
        if ( ! $this->iterator->valid()) {
            return null;
        }

        $resonse = $this->iterator->current();
        $json    = $resonse->json();

        if ( ! isset($json['data'])) {
            return null;
        }

        $response = $json['data'];
        $entry    = new MapReduceEntry();
        $phase    = isset($json['phase']) ? $json['phase'] : 0;

        $entry->phase    = $phase;
        $entry->response = $response;

        return $entry;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();

        parent::next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        parent::rewind();
    }
}
