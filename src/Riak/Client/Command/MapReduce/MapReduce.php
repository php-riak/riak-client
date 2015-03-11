<?php

namespace Riak\Client\Command\MapReduce;

use Riak\Client\RiakCommand;
use Riak\Client\Core\Query\Func\RiakFunction;
use Riak\Client\Command\MapReduce\Specification;

/**
 * Base abstract class for all MapReduce commands.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class MapReduce implements RiakCommand
{
    /**
     * @var \Riak\Client\Command\MapReduce\Specification
     */
    protected $specification;

    /**
     * @param \Riak\Client\Command\MapReduce\Specification $specification
     */
    public function __construct(Specification $specification)
    {
        $this->specification = $specification;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\Specification
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * @param \Riak\Client\Command\MapReduce\Specification $specification
     */
    public function setSpecification(RiakFunction $specification)
    {
        $this->specification = $specification;
    }
}
