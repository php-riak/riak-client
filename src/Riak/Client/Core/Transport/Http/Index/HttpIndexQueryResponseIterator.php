<?php

namespace Riak\Client\Core\Transport\Http\Index;

use ArrayIterator;
use RuntimeException;
use Riak\Client\Core\Message\Index\IndexEntry;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Http\MultipartResponseIterator;

/**
 * Http index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpIndexQueryResponseIterator implements \Iterator
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
    private $current;

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
            return;
        }

        $body = $this->iterator->current();
        $json = $body->json();

        if (isset($json['results'])) {
            return $this->iteratorFromResults($json['results']);
        }

        if (isset($json['keys'])) {
            return $this->iteratorFromKeys($json['keys']);
        }

        throw new RuntimeException("Invalid iterator element");
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
    public function next()
    {
        $this->iterator->next();

        $this->current = $this->readNext();
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return ($this->current !== null);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        $this->current = $this->readNext();
    }
}
