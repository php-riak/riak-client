<?php

namespace Riak\Client\Core\Transport\Proto\Kv;

use ArrayIterator;
use Protobuf\Message;
use Riak\Client\Core\Transport\Proto\ProtoStreamIteratorIterator;

/**
 * RPB List keys response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ProtoListKeysResponseIterator extends ProtoStreamIteratorIterator
{
    /**
     * {@inheritdoc}
     */
    protected function extract(Message $message)
    {
        if ( ! $message->hasKeysList()) {
            return null;
        }

        return new ArrayIterator($message->getKeysList());
    }
}
