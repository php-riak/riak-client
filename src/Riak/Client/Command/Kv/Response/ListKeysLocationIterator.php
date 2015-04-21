<?php

namespace Riak\Client\Command\Kv\Response;

use Iterator;
use Riak\Client\Core\RiakEntryIterator;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;

/**
 * A Riak list keys reponse iterator.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListKeysLocationIterator extends RiakEntryIterator
{
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

        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     */
    protected function currentInnerEntry()
    {
        $key      = $this->innerIterator->current();
        $location = new RiakLocation($this->namespace, $key);

        return $location;
    }
}
