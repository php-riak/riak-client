<?php

namespace Riak\Client\Core\Transport\Proto\Index;

use Traversable;
use ArrayIterator;
use Protobuf\Message;
use Riak\Client\Core\RiakContinuableIterator;
use Riak\Client\Core\Message\Index\IndexEntry;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Proto\ProtoStreamIterator;
use Riak\Client\Core\Transport\Proto\ProtoStreamIteratorIterator;

/**
 * RPB index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoIndexQueryResponseIterator extends ProtoStreamIteratorIterator implements RiakContinuableIterator
{
    /**
     * @var \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     */
    private $request;

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest     $request
     * @param \Riak\Client\Core\Transport\Proto\ProtoStreamIterator $iterator
     */
    public function __construct(IndexQueryRequest $request, ProtoStreamIterator $iterator)
    {
        $this->request = $request;

        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContinuation()
    {
        return $this->message && $this->message->hasContinuation();
    }

    /**
     * {@inheritdoc}
     */
    public function getContinuation()
    {
        if ( ! $this->hasContinuation()) {
            return null;
        }

        return $this->message->getContinuation();
    }

    /**
     * {@inheritdoc}
     */
    protected function extract(Message $message)
    {
        if ($message->hasResultsList()) {
            return $this->iteratorFromResults($message->getResultsList());
        }

        if ($message->hasKeysList()) {
            return $this->iteratorFromKeys($message->getKeysList());
        }

        return null;
    }

    /**
     * @param \Traversable|array $results
     *
     * @return Traversable
     */
    private function iteratorFromResults($results)
    {
        $values = [];

        foreach ($results as $pair) {
            $entry = new IndexEntry();

            $entry->indexKey  = $pair->getKey()->getContents();
            $entry->objectKey = $pair->getValue()->getContents();

            $values[] = $entry;
        }

        return new ArrayIterator($values);
    }

    /**
     * @param \Traversable|array $keys
     *
     * @return Traversable
     */
    private function iteratorFromKeys($keys)
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
