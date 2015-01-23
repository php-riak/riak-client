<?php

namespace Riak\Client\Converter;

use Riak\Client\Converter\Hydrator\DomainHydrator;

/**
 * Holds instances of converters to be used for serialization / deserialization  of objects stored and fetched from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ConverterFactory
{
    /**
     * @var \Riak\Client\Converter\Converter[]
     */
    private $converters;

    /**
     * @var \Riak\Client\Converter\Hydrator\DomainHydrator
     */
    private $domainHydrator;

    /**
     * @param \Riak\Client\Converter\Hydrator\DomainHydrator $domainHydrator
     */
    public function __construct(DomainHydrator $domainHydrator)
    {
        $this->domainHydrator = $domainHydrator;
    }

    /**
     * @return \Riak\Client\Converter\Converter[]
     */
    public function getConverters()
    {
        return $this->converters;
    }

    /**
     * @param \Riak\Client\Converter\Converter[] $converters
     */
    public function setConverters(array $converters)
    {
        $this->converters = $converters;
    }

    /**
     * @param string $type
     *
     * @return \Riak\Client\Converter\Converter
     */
    public function getConverter($type)
    {
        if (isset($this->converters[$type])) {
            return $this->converters[$type];
        }

        return $this->converters[$type] = new JsonConverter($this->domainHydrator);
    }

    /**
     * @param string                           $type
     * @param \Riak\Client\Converter\Converter $converter
     */
    public function addConverter($type, Converter $converter)
    {
        $this->converters[$type] = $converter;
    }
}
