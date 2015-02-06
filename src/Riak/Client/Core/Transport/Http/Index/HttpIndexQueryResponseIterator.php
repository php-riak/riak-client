<?php

namespace Riak\Client\Core\Transport\Http\Index;

use ArrayIterator;
use Riak\Client\Core\RiakIterator;
use Riak\Client\Core\RiakContinuableIterator;
use Riak\Client\Core\Message\Index\IndexEntry;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Http\MultipartResponseIterator;

/**
 * Http index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpIndexQueryResponseIterator extends RiakIterator implements RiakContinuableIterator
{
    /**
     * @var \Riak\Client\Core\Transport\Http\MultipartResponseIterator
     */
    private $iterator;

    /**
     * @var \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     */
    private $request;

    /**
     * @var array
     */
    private $currentJson;

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest          $request
     * @param \Riak\Client\Core\Transport\Http\MultipartResponseIterator $iterator
     */
    public function __construct(IndexQueryRequest $request, MultipartResponseIterator $iterator)
    {
        $this->request  = $request;
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function hasContinuation()
    {
        return isset($this->currentJson['continuation']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContinuation()
    {
        if ( ! $this->hasContinuation()) {
            return null;
        }

        return $this->currentJson['continuation'];
    }

    /**
     * @param array $results
     *
     * @return array
     */
    private function iteratorFromResults(array $results)
    {
        $values = [];

        foreach ($results as $item) {
            $entry = new IndexEntry();

            $entry->indexKey  = key($item);
            $entry->objectKey = reset($item);

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
        $values   = [];
        $indexKey  = ($this->request->qtype === 'eq')
            ? $this->request->key
            : null;

        foreach ($keys as $value) {
            $entry = new IndexEntry();

            $entry->indexKey  = $indexKey;
            $entry->objectKey = $value;

            $values[] = $entry;
        }

        return new ArrayIterator($values);
    }

    /**
     * {@inheritdoc}
     */
    public function readNext()
    {
        if ( ! $this->iterator->valid()) {
            return null;
        }

        $body = $this->iterator->current();
        $json = $body->json();

        $this->currentJson = $json;

        if (isset($json['results'])) {
            return $this->iteratorFromResults($json['results']);
        }

        if (isset($json['keys'])) {
            return $this->iteratorFromKeys($json['keys']);
        }

        return null;
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
