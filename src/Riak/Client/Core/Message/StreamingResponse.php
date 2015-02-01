<?php

namespace Riak\Client\Core\Message;

/**
 * Base class for all responses that use streaming.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StreamingResponse extends Response
{
    /**
     * @var \Iterator
     */
    public $iterator;
}
