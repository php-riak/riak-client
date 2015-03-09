<?php

namespace Riak\Client\Command\MapReduce\Phase;

use JsonSerializable;

/**
 * Base class for Map/Reduce phase definitions.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class MapReducePhase
{
    const MAP = 'map';
    const LINK = 'link';
    const REDUCE = 'reduce';

    /**
     * @var boolean
     */
    protected $keepResult;

    /**
     * Is this phase's output to be returned or only passed as input to the next phase.
     *
     * @return boolean
     */
    public function getKeepResult()
    {
        return $this->keepResult;
    }

    /**
     * Set whether this should be kept
     *
     * @param boolean $keepResult
     */
    public function setKeepResult($keepResult)
    {
        $this->keepResult = $keepResult;
    }

    /**
     * The type of this Phase implementation.
     *
     * @return string
     */
    abstract public function getPhaseName();
}
