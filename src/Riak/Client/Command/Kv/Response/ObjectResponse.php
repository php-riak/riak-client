<?php

namespace Riak\Client\Command\Kv\Response;

use Riak\Client\Core\Query\RiakList;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Resolver\ResolverFactory;
use Riak\Client\Converter\ConverterFactory;
use Riak\Client\Core\Query\DomainObjectList;
use Riak\Client\Converter\RiakObjectReference;

/**
 * Riak Object response
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class ObjectResponse extends Response
{
    /**
     * @var \Riak\Client\Converter\ConverterFactory
     */
    private $converterFactory;

    /**
     * @var \Riak\Client\Resolver\ResolverFactory
     */
    private $resolverFactory;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Riak\Client\Core\Query\RiakList
     */
    private $values;

    /**
     * @param \Riak\Client\Converter\ConverterFactory $converterFactory
     * @param \Riak\Client\Resolver\ResolverFactory   $resolverFactory
     * @param \Riak\Client\Core\Query\RiakLocation    $location
     * @param \Riak\Client\Core\Query\RiakList        $values
     */
    public function __construct(ConverterFactory $converterFactory, ResolverFactory $resolverFactory, RiakLocation $location, RiakList $values)
    {
        $this->converterFactory = $converterFactory;
        $this->resolverFactory  = $resolverFactory;
        $this->location         = $location;
        $this->values           = $values;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Determine if this response contains any returned values.
     *
     * @return boolean.
     */
    public function hasValues()
    {
        return ( ! $this->values->isEmpty());
    }

    /**
     * Return the number of values contained in this response.
     *
     * @return integer
     */
    public function getNumberOfValues()
    {
        return $this->values->count();
    }

    /**
     * Get all the objects returned in this response.
     * If a type is provided will be converted to the supplied class using the convert api.
     *
     * @param string $type
     *
     * @return \Riak\Client\Core\Query\RiakList
     */
    public function getValues($type = null)
    {
        if ($type == null) {
            return $this->values;
        }

        $converter  = $this->converterFactory->getConverter($type);
        $resultList = [];

        foreach ($this->values as $riakObject) {
            $reference = new RiakObjectReference($riakObject, $this->location, $type);
            $converted = $converter->toDomain($reference);

            $resultList[] = $converted;
        }

        return new DomainObjectList($resultList);
    }

    /**
     * Get a single, resolved object from this response.
     *
     * @param string $type
     *
     * @return \Riak\Client\Core\Query\RiakObject|object
     *
     * @throws \Riak\Client\Resolver\UnresolvedConflictException
     */
    public function getValue($type = null)
    {
        $siblings   = $this->getValues($type);
        $resolver   = $this->resolverFactory->getResolver($type);
        $riakObject = $resolver->resolve($siblings);

        return $riakObject;
    }

    /**
     * Get the vector clock returned with this response.
     *
     * @return \Riak\Client\Core\Query\VClock
     */
    public function getVectorClock()
    {
        if ($this->values->isEmpty()) {
            return;
        }

        $first  = $this->values->first();
        $vclock = $first->getVClock();

        return $vclock;
    }
}
