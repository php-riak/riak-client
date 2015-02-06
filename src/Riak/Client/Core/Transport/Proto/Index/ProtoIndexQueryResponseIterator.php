<?php

namespace Riak\Client\Core\Transport\Proto\Index;

use ArrayIterator;
use Riak\Client\Core\RiakIterator;
use Riak\Client\Core\RiakContinuableIterator;
use Riak\Client\Core\Message\Index\IndexEntry;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;

/**
 * RPB index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoIndexQueryResponseIterator extends RiakIterator implements RiakContinuableIterator
{
    /**
     * @var \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     */
    private $request;

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoStreamIterator $iterator
     */
    private $iterator;

    /**
     * @var \Riak\Client\ProtoBuf\RpbIndexResp
     */
    private $currentMessage;

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest     $request
     * @param \Riak\Client\Core\Transport\Proto\ProtoStreamIterator $iterator
     */
    public function __construct(IndexQueryRequest $request, ProtoStreamIterator $iterator)
    {
        $this->request  = $request;
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function hasContinuation()
    {
        return $this->currentMessage && $this->currentMessage->hasContinuation();
    }

    /**
     * {@inheritdoc}
     */
    public function getContinuation()
    {
        if ( ! $this->hasContinuation()) {
            return null;
        }

        return $this->currentMessage->continuation;
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

        if ($this->currentMessage->hasResults()) {
            return $this->iteratorFromResults($this->currentMessage->results);
        }

        if ($this->currentMessage->hasKeys()) {
            return $this->iteratorFromKeys($this->currentMessage->keys);
        }

        return null;
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

    /**
     * @param array $results
     *
     * @return array
     */
    private function iteratorFromResults(array $results)
    {
        $values = [];

        foreach ($results as $pair) {
            $entry = new IndexEntry();

            $entry->indexKey  = $pair->key;
            $entry->objectKey = $pair->value;

            $values[] = $entry;
        }

        return new ArrayIterator($values);
    }

    /**
     * @param array $keys
     *
     * @return array
     */
    private function iteratorFromKeys(array $keys)
    {
        $values = [];
        $key    = ($this->request->qtype === 'eq')
            ? $this->request->key
            : null;

        foreach ($keys as $value) {
            $entry = new IndexEntry();

            $entry->indexKey  = $key;
            $entry->objectKey = $value;

            $values[] = $entry;
        }

        return new ArrayIterator($values);
    }
}
