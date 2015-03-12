<?php

namespace Riak\Client\Command\Bucket\Response;

use Riak\Client\Core\RiakEntryIterator;

/**
 * A Riak list buckets reponse iterator.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListBucketsIterator extends RiakEntryIterator
{
    /**
     * {@inheritdoc}
     */
    protected function currentInnerEntry()
    {
        return $this->innerIterator->current();
    }
}
