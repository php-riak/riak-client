<?php

namespace RiakClientFixture\Resolver;

use Riak\Client\Resolver\ConflictResolver;
use Riak\Client\Core\Query\RiakList;

class SimpleObjectConflictResolver implements ConflictResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(RiakList $siblings)
    {
        /** @var $result \RiakClientFixture\Domain\SimpleObject */
        $result = clone $siblings->first();
        $values = "";

        /** @var $object \RiakClientFixture\Domain\SimpleObject */
        foreach ($siblings as $object) {
            $values[] = $object->getValue();
        }

        $result->setValue(implode($values, PHP_EOL));

        return $result;
    }
}
