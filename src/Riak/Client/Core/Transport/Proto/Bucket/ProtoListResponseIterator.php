<?php

namespace Riak\Client\Core\Transport\Proto\Bucket;

use ArrayIterator;
use Protobuf\Message;
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
        if ( ! $message->hasBucketsList()) {
            return null;
        }

        return new ArrayIterator($message->getBucketsList());
    }
}
