<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\RiakCommand;
use Riak\Client\Core\Query\RiakLocation;

/**
 * Command used to update or create a counter datatype in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class StoreDataType implements RiakCommand
{
    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    protected $location;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var \Riak\Client\Command\DataType\DataTypeUpdate
     */
    protected $update;

    /**
     * @var string
     */
    protected $context;

    /**
     * @param \Riak\Client\Core\Query\RiakLocation         $location
     * @param \Riak\Client\Command\DataType\DataTypeUpdate $update
     * @param array                                        $options
     */
    public function __construct(RiakLocation $location, DataTypeUpdate $update, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->update   = $update;
    }

    /**
     * Include the context from a previous fetch.
     *
     * @param string $context
     *
     * @return \Riak\Client\Command\DataType\StoreDataType
     */
    public function withContext($context)
    {
        $this->context = $context;

        return $this;
    }
}
