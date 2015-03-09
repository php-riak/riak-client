<?php

namespace Riak\Client\Command\MapReduce\Phase;

use Riak\Client\Core\Query\Func\RiakFunction;

/**
 * Phase containing a function
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class FunctionPhase extends MapReducePhase
{
    /**
     * @var \Riak\Client\Core\Query\Func\RiakFunction
     */
    private $function;

    /**
     * @var mixed
     */
    private $argument;

    /**
     * @param \Riak\Client\Core\Query\Func\RiakFunction $function
     * @param mixed                                     $argument
     * @param boolean                                   $keepResult
     */
    public function __construct(RiakFunction $function, $argument = null, $keepResult = false)
    {
        $this->keepResult = $keepResult;
        $this->function   = $function;
        $this->argument   = $argument;
    }

    /**
     * @return \Riak\Client\Core\Query\Func\RiakFunction
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @return midex
     */
    public function getArg()
    {
        return $this->argument;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $function = $this->function->jsonSerialize();
        $data     = ['keep' => $this->keepResult];

        if ($this->argument) {
            $data['arg'] = $this->argument;
        }

        return array_merge($function, $data);
    }
}
