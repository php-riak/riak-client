<?php

namespace Riak\Client\Core\Transport\Proto\Index;

use ArrayIterator;
use RuntimeException;
use GuzzleHttp\Stream\Stream;
use Riak\Client\ProtoBuf\RiakMessageCodes;
use Riak\Client\Core\Message\Index\IndexEntry;
use Riak\Client\Core\Transport\Proto\ProtoClient;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Proto\ProtoStreamingResponseIterator;

/**
 * RPB index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoIndexQueryResponseIterator extends ProtoStreamingResponseIterator
{
    /**
     * @var \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     */
    private $request;

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient     $client
     * @param \GuzzleHttp\Stream\Stream                         $stream
     */
    public function __construct(IndexQueryRequest $request, ProtoClient $client, Stream $stream)
    {
        $this->request = $request;

        parent::__construct($client, $stream, RiakMessageCodes::INDEX_RESP);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        if ($this->current === null) {
            return false;
        }

        if ($this->current->hasDone() && $this->current->done) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if ($this->current->hasResults()) {
            return $this->iteratorFromResults($this->current->results);
        }

        if ($this->current->hasKeys()) {
            return $this->iteratorFromKeys($this->current->keys);
        }

        throw new RuntimeException("Invalid iterator element");
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
