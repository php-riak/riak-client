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
     * @param string                                       $context
     * @param array                                        $options
     */
    public function __construct(RiakLocation $location, DataTypeUpdate $update, $context, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
        $this->context  = $context;
        $this->update   = $update;
    }
}
