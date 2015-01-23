<?php

namespace Riak\Client\Converter;

use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;

/**
 * Encapsulates a Riak object.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakObjectReference
{
    /**
     * @var \Riak\Client\Core\Query\RiakObject
     */
    private $riakObject;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var string
     */
    private $domainObjectType;

    /**
     * @param \Riak\Client\Core\Query\RiakObject   $riakObject
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param string                               $type
     */
    public function __construct(RiakObject $riakObject, RiakLocation $location, $type = null)
    {
        $this->riakObject       = $riakObject;
        $this->location         = $location;
        $this->domainObjectType = $type;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakObject
     */
    public function getRiakObject()
    {
        return $this->riakObject;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getDomainObjectType()
    {
        return $this->domainObjectType;
    }
}
