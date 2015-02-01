<?php

namespace Riak\Client\Command\Index\Response;

use Iterator;
use Riak\Client\RiakResponse;
use Riak\Client\Core\Query\RiakNamespace;

/**
 * Base response for 2i index queries.
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
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var string
     */
    private $continuation;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param \Iterator                             $iterator
     * @param string                                $continuation
     */
    public function __construct(RiakNamespace $namespace, Iterator $iterator, $continuation = null)
    {
        $this->continuation = $continuation;
        $this->namespace    = $namespace;
        $this->iterator     = $iterator;
    }

    /**
     * @return \Iterator
     */
    public function getEntries()
    {
        return $this->iterator;
    }
}
