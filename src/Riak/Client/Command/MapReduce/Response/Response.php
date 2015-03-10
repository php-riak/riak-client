<?php

namespace Riak\Client\Command\MapReduce\Response;

use Iterator;
use Riak\Client\RiakResponse;

/**
 * Base response for Map-Reduce commands.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }
}
