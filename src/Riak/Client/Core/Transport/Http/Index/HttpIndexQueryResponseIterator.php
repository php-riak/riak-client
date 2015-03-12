<?php

namespace Riak\Client\Core\Transport\Http\Index;

use ArrayIterator;
use Riak\Client\Core\RiakContinuableIterator;
use Riak\Client\Core\Message\Index\IndexEntry;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Http\MultipartResponseIterator;
use Riak\Client\Core\Transport\Http\MultipartResponseIteratorIterator;

/**
 * Http index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class HttpIndexQueryResponseIterator extends MultipartResponseIteratorIterator implements RiakContinuableIterator
{
    /**
     * @var \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     */
    private $request;

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest          $request
     * @param \Riak\Client\Core\Transport\Http\MultipartResponseIterator $iterator
     */
    public function __construct(IndexQueryRequest $request, MultipartResponseIterator $iterator)
    {
        $this->request = $request;

        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContinuation()
    {
        return isset($this->json['continuation']);
    }

    /**
     * {@inheritdoc}
     */
    public function getContinuation()
    {
        if ( ! $this->hasContinuation()) {
            return null;
        }

        return $this->json['continuation'];
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
    public function extract($json)
    {
        if (isset($json['results'])) {
            return $this->iteratorFromResults($json['results']);
        }

        if (isset($json['keys'])) {
            return $this->iteratorFromKeys($json['keys']);
        }

        return null;
    }
}
