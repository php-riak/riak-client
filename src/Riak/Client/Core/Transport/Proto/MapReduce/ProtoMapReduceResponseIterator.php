<?php

namespace Riak\Client\Core\Transport\Proto\MapReduce;

use Riak\Client\Core\RiakIterator;
use Riak\Client\Core\Message\MapReduce\MapReduceEntry;
use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;

/**
 * RPB Map-Reduce response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoMapReduceResponseIterator extends RiakIterator
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoStreamIterator $iterator
     */
    private $iterator;

    /**
     * @var \Riak\Client\ProtoBuf\RpbMapRedResp
     */
    private $currentMessage;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoStreamIterator $iterator
     */
    public function __construct(ProtoStreamIterator $iterator)
    {
        $this->iterator = $iterator;
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

    /**
     * @return \Iterator
     */
    public function readNext()
    {
        if ($this->isDone() || ! $this->iterator->valid()) {
            return null;
        }

        $this->currentMessage = $this->iterator->current();

        $phase = 0;

        if ($this->currentMessage->hasPhase()) {
            $phase = $this->currentMessage->phase;
        }

        if ( ! $this->currentMessage->hasResponse()) {
            return null;
        }

        $response = json_decode($this->currentMessage->response, true);
        $entry    = new MapReduceEntry();

        $entry->phase    = $phase;
        $entry->response = $response;

        return $entry;
    }

    /**
     * @return boolean
     */
    private function isDone()
    {
        if ($this->currentMessage === null) {
            return false;
        }

        if ($this->currentMessage->hasDone() && $this->currentMessage->done) {
            return true;
        }

        return false;
    }
}
