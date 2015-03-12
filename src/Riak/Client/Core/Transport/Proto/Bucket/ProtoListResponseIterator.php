<?php

namespace Riak\Client\Core\Transport\Proto\Bucket;

use ArrayIterator;
use DrSlump\Protobuf\Message;
use Riak\Client\Core\Transport\Proto\ProtoStreamIteratorIterator;

/**
 * RPB bucket list response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoListResponseIterator extends ProtoStreamIteratorIterator
{
    /**
     * {@inheritdoc}
     */
    protected function extract(Message $message)
    {
        if ( ! $message->hasBuckets()) {
            return null;
        }

        return new ArrayIterator($message->buckets);
    }
}
