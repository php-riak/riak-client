<?php

namespace Riak\Client\Command\MapReduce\Input\BucketKey;

use JsonSerializable;
use Riak\Client\Core\Query\RiakLocation;

/**
 * Map-Reduce Individual Input
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Input implements JsonSerializable
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation $location
     * @param mixed                                $data
     */
    public function __construct(RiakLocation $location, $data = null)
    {
        $this->location = $location;
        $this->data     = $data;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $namespace  = $this->location->getNamespace();
        $data       = [
            $namespace->getBucketName(),
            $this->location->getKey(),
            $this->data ?: ""
        ];

        if ( ! $namespace->isDefaultType()) {
            $data[] = $namespace->getBucketType();
        }

        return $data;
    }
}
