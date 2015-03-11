<?php

namespace Riak\Client\Command\MapReduce\Response;

use Iterator;
use Riak\Client\RiakResponse;

/**
 * Base response for Map-Reduce commands.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Response implements RiakResponse
{
    /**
     * @var \Iterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $results;

    /**
     * @param \Iterator $iterator
     */
    public function __construct(Iterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->iterator;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        if ($this->results !== null) {
            return $this->results;
        }

        foreach ($this->iterator as $entry) {
            $phase    = $entry->getPhase();
            $response = $entry->getResponse();

            foreach ($response as $value) {
                $this->results[$phase][] = $value;
            }
        }

        return $this->results;
    }

    /**
     * @param integer $phase
     *
     * @return array
     */
    public function getResultForPhase($phase)
    {
        $results      = $this->getResults();
        $phaseResults = isset($results[$phase]) ? $results[$phase] : null;

        return $phaseResults;
    }

    /**
     * @return array
     */
    public function getResultsFromAllPhases()
    {
        return call_user_func_array('array_merge', $this->getResults());
    }
}
