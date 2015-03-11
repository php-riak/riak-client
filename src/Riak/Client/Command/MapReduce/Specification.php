<?php

namespace Riak\Client\Command\MapReduce;

use JsonSerializable;
use Riak\Client\Command\MapReduce\Phase\MapReducePhase;
use Riak\Client\Command\MapReduce\Input\MapReduceInput;

/**
 * Map-Reduce specicifcation
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Specification implements JsonSerializable
{
    /**
     * @var \Riak\Client\Command\MapReduce\MapReduceInput
     */
    private $input;

    /**
     * @var \Riak\Client\Command\MapReduce\MapReducePhase[]
     */
    private $phases = [];

    /**
     * @var integer
     */
    private $timeout;

    /**
     * @param \Riak\Client\Command\MapReduce\MapReduceInput   $input
     * @param \Riak\Client\Command\MapReduce\MapReducePhase[] $phases
     * @param integer                                         $timeout
     */
    public function __construct(MapReduceInput $input, array $phases = [], $timeout = null)
    {
        $this->input   = $input;
        $this->phases  = $phases;
        $this->timeout = $timeout;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\MapReduceInput
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\MapReducePhase[]
     */
    public function getPhases()
    {
        return $this->phases;
    }

    /**
     * @param \Riak\Client\Command\MapReduce\MapReducePhase $phase
     */
    public function addPhase(MapReducePhase $phase)
    {
        $this->phases[] = $phase;
    }

    /**
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param integer $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $input  = $this->input ? $this->input->jsonSerialize() : null;
        $phases = array_map(function (MapReducePhase $phase) {
            return [$phase->getPhaseName() => $phase->jsonSerialize()];
        }, $this->phases);

        return array_filter([
            'inputs'  => $input,
            'query'   => $phases,
            'timeout' => $this->timeout,
        ]);
    }
}
