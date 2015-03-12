<?php

namespace Riak\Client\Command\Index\Response;

use Riak\Client\Core\RiakEntryIterator;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\RiakContinuableIterator;
use Riak\Client\Command\Index\Response\IndexEntry;

/**
 * A Riak 2i index reponse iterator.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IndexEntryIterator extends RiakEntryIterator implements RiakContinuableIterator
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace                        $namespace
     * @param \Riak\Client\Core\Transport\RiakTransportContinuableIterator $iterator
     */
    public function __construct(RiakNamespace $namespace, RiakContinuableIterator $iterator)
    {
        $this->namespace = $namespace;

        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function hasContinuation()
    {
        return $this->iterator->hasContinuation();
    }

    /**
     * {@inheritdoc}
     */
    public function getContinuation()
    {
        return $this->iterator->getContinuation();
    }

    /**
     * {@inheritdoc}
     */
    protected function currentInnerEntry()
    {
        $entry    = $this->innerIterator->current();
        $location = new RiakLocation($this->namespace, $entry->objectKey);
        $current  = new IndexEntry($location, $entry->indexKey);

        return $current;
    }
}
