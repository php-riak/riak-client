<?php

namespace Riak\Client\Converter;

use ReflectionClass;

/**
 * The default Converter used when storing and fetching domain objects from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class JsonConverter extends BaseConverter
{
    /**
     * {@inheritdoc}
     */
    protected function fromDomainObject($domainObject)
    {
        return json_encode($domainObject);
    }

    /**
     * {@inheritdoc}
     */
    protected function toDomainObject($value, $type)
    {
        $reflection = new ReflectionClass($type);
        $data       = json_decode($value, true);
        $object     = is_array($data)
            ? $reflection->newInstance($data)
            : $reflection->newInstance();

        if ( ! is_array($data)) {
            return $object;
        }

        foreach ($data as $key => $value) {
            call_user_func([$object , 'set'. ucfirst($key)], $value);
        }

        return $object;
    }
}
