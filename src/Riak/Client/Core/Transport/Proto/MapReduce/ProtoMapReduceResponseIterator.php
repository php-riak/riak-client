<?php

namespace Riak\Client\Core\Transport\Proto\MapReduce;

use DrSlump\Protobuf\Message;
use Riak\Client\Core\Message\MapReduce\MapReduceEntry;
use Riak\Client\Core\Transport\Proto\ProtoStreamIteratorIterator;

/**
 * RPB Map-Reduce response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoMapReduceResponseIterator extends ProtoStreamIteratorIterator
{
    /**
     * {@inheritdoc}
     */
    protected function extract(Message $message)
    {
        if ( ! $message->hasResponse()) {
            return null;
        }

        $phase    = $message->hasPhase() ? $message->phase : 0;
        $response = json_decode($message->response, true);
        $entry    = new MapReduceEntry();

        $entry->phase    = $phase;
        $entry->response = $response;

        return $entry;
    }
}
