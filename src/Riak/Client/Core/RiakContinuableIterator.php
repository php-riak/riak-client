<?php

namespace Riak\Client\Core;

use Iterator;

/**
 * Riak continuable iterator
 *
 * An iterator that provides a continuation hash for the next operation.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface RiakContinuableIterator extends Iterator
{
    /**
     * @return boolean
     */
    public function hasContinuation();

    /**
     * @return string
     */
    public function getContinuation();
}
