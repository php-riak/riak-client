<?php

namespace Riak\Client\Command\DataType;

use Riak\Client\RiakCommand;
use Riak\Client\Core\Query\RiakLocation;

/**
 * Command used to fetch a datatype from Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class FetchDataType implements RiakCommand
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
     * @param \Riak\Client\Command\Kv\RiakLocation $location
     * @param array                                $options
     */
    public function __construct(RiakLocation $location, array $options = [])
    {
        $this->location = $location;
        $this->options  = $options;
    }
}
