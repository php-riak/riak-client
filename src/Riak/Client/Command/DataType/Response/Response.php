<?php

namespace Riak\Client\Command\DataType\Response;

use Riak\Client\RiakResponse;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\Crdt\DataType;

/**
 * Base Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    private $location;

    /**
     * @var \Riak\Client\Core\Query\Crdt\DataType
     */
    private $datatype;

    /**
     * @var string
     */
    protected $context;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation  $location
     * @param \Riak\Client\Core\Query\Crdt\DataType $datatype
     * @param string                                $context
     */
    public function __construct(RiakLocation $location = null, DataType $datatype = null, $context = null)
    {
        $this->datatype = $datatype;
        $this->location = $location;
        $this->context  = $context;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakLocation
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Get the datatype from this response.
     *
     * @return \Riak\Client\Core\Query\Crdt\DataType
     */
    public function getDatatype()
    {
        return $this->datatype;
    }

    /**
     * Get the Data Type context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }
}
