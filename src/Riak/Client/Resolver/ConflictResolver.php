<?php

namespace Riak\Client\Resolver;

use Riak\Client\Core\Query\RiakList;

/**
 * Interface used to resolve siblings.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface ConflictResolver
{
    /**
     * Resolve a set a of siblings to a single object.
     *
     * @param \Riak\Client\Core\Query\RiakList $siblings
     *
     * @return \Riak\Client\Core\Query\RiakObject
     *
     * @throws \Riak\Client\Resolver\UnresolvedConflictException
     */
    public function resolve(RiakList $siblings);
}
