<?php

namespace Riak\Client\Converter;

use Riak\Client\Core\Query\RiakLocation;

/**
 * Encapsulates a domain object.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class DomainObjectReference
{
    /**
     * @var mixed
     */
    private $domainObject;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @param mixed                                $domainObject
     * @param \Riak\Client\Core\Query\RiakLocation $location
     */
    public function __construct($domainObject, RiakLocation $location)
    {
        $this->domainObject = $domainObject;
        $this->location     = $location;
    }

    /**
     * @return mixed
     */
    public function getDomainObject()
    {
        return $this->domainObject;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }
}
