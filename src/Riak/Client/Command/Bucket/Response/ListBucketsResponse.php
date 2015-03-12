<?php

namespace Riak\Client\Command\Bucket\Response;

use Iterator;

/**
 * List Buckets Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ListBucketsResponse extends Response
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $buckets;

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

    /**
     * @return array
     */
    public function getBuckets()
    {
        if ($this->buckets === null) {
            $this->buckets = iterator_to_array($this->iterator);
        }

        return $this->buckets;
    }
}
