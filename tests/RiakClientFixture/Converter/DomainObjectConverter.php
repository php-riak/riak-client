<?php

namespace RiakClientFixture\Converter;

use Riak\Client\Converter\BaseConverter;
use RiakClientFixture\Domain\DomainObject;

class DomainObjectConverter extends BaseConverter
{
    /**
     * {@inheritdoc}
     */
    protected function fromDomainObject($domainObject)
    {
        return implode(',', $domainObject->getValues());
    }

    /**
     * {@inheritdoc}
     */
    protected function toDomainObject($value, $type)
    {
        if ($type !== DomainObject::CLASS_NAME) {
            throw new \InvalidArgumentException();
        }

        $values = explode(',', $value);
        $object = new DomainObject($values);

        return $object;
    }
}
