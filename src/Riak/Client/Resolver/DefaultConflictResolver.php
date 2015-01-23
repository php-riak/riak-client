<?php

namespace Riak\Client\Resolver;

use Riak\Client\Core\Query\RiakList;

/**
 * A conflict resolver that doesn't resolve conflict
 * If it is presented with a collection of siblings with more than one entry it throws an Exception
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DefaultConflictResolver implements ConflictResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(RiakList $siblings)
    {
        if (count($siblings) == 1) {
            return $siblings->first();
        }

        if ($siblings->isEmpty()) {
            return;
        }

        throw new UnresolvedConflictException();
    }
}
