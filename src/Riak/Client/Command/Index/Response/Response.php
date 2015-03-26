<?php

namespace Riak\Client\Command\Index\Response;

use Riak\Client\RiakResponse;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\RiakContinuableIterator;

/**
 * Base response for 2i index queries.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Riak\Client\Core\RiakContinuableIterator
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
     * @param \Riak\Client\Core\Query\RiakNamespace     $namespace
     * @param \Riak\Client\Core\RiakContinuableIterator $iterator
     */
    public function __construct(RiakNamespace $namespace, RiakContinuableIterator $iterator)
    {
        $this->namespace = $namespace;
        $this->iterator  = $iterator;
    }

    /**
     * @return boolean
     */
    public function hasContinuation()
    {
        return $this->iterator->hasContinuation();
    }

    /**
     * @return string
     */
    public function getContinuation()
    {
        return $this->iterator->getContinuation();
    }

    /**
     * @return \Riak\Client\Core\RiakContinuableIterator
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
