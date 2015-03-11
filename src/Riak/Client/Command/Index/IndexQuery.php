<?php

namespace Riak\Client\Command\Index;

use Riak\Client\RiakCommand;
use Riak\Client\Core\Query\RiakIndexQuery;

/**
 * A Secondary Index Query.
 * Serves as a base class for all 2i queries.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class IndexQuery implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakIndexQuery
     */
    protected $query;

    /**
     * @param \Riak\Client\Core\Query\RiakIndexQuery $query
     */
    public function __construct(RiakIndexQuery $query)
    {
        $this->query = $query;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakIndexQuery
     */
    public function getQuery()
    {
        return $this->query;
    }
}
