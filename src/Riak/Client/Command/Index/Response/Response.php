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
     * @var array
     */
    private $entries;

    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param \Iterator                             $iterator
     */
    public function __construct(RiakNamespace $namespace, Iterator $iterator)
    {
        $this->namespace = $namespace;
        $this->iterator  = $iterator;
    }

    /**
     * @return string
     */
    public function getContinuation()
    {
        return $this->iterator->getContinuation();
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        if ($this->entries === null) {
            $this->entries = iterator_to_array($this->iterator);
        }

        return $this->entries;
    }
}
