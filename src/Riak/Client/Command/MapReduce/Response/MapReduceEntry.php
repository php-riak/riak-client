<?php

namespace Riak\Client\Command\MapReduce\Response;

/**
 * Riak MapReduce entry.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class MapReduceEntry
{
    /**
     * @var integer
     */
    private $phase;

    /**
     * @var array
     */
    private $response;

    /**
     * @param integer $phase
     * @param array   $response
     */
    public function __construct($phase, array $response)
    {
        $this->phase    = $phase;
        $this->response = $response;
    }

    /**
     * @return integer
     */
    public function getPhase()
    {
        return $this->phase;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }
}
