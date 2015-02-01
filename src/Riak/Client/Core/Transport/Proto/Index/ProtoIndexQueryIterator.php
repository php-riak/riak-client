<?php

namespace Riak\Client\Core\Transport\Proto\Index;

use ArrayIterator;
use Riak\Client\ProtoBuf\RpbIndexResp;
use Riak\Client\Core\Message\Index\IndexEntry;
use Riak\Client\Core\Transport\Proto\ProtoClient;
use Riak\Client\Core\Message\Index\IndexQueryRequest;
use Riak\Client\Core\Transport\Proto\ProtoStreamingResponseIterator;

/**
 * RPB index query response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoIndexQueryIterator extends ProtoStreamingResponseIterator
{
    /**
     * @var \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     */
    private $request;

    /**
     * @param \Riak\Client\Core\Message\Index\IndexQueryRequest $request
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient     $client
     * @param integer                                           $messageCode
     */
    public function __construct(IndexQueryRequest $request, ProtoClient $client, $messageCode)
    {
        $this->request = $request;

        parent::__construct($client, $messageCode);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        if ($this->current === null || ( ! $this->current instanceof RpbIndexResp )) {
            return false;
        }

        if ($this->current->hasDone() && $this->current->done) {
            return false;
        }

        return true;
    }

    /**
     * @param array $results
     *
     * @return array
     */
    public function currentFromResults(array $results)
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
    public function currentFromKeys(array $keys)
    {
        $values = [];

        foreach ($keys as $value) {
            $entry = new IndexEntry();

            $entry->indexKey  = $this->request->key;
            $entry->objectKey = $value;

            $values[] = $entry;
        }

        return new ArrayIterator($values);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if ($this->current->hasResults()) {
            return $this->currentFromResults($this->current->results);
        }

        if ($this->current->hasKeys()) {
            return $this->currentFromKeys($this->current->keys);
        }

        throw new \RuntimeException("Invalid iterator element");
    }
}
