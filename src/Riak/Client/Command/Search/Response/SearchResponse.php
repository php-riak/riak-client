<?php

namespace Riak\Client\Command\Search\Response;

/**
 * Yokozuna Search Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchResponse extends Response
{
    /**
     * @var array
     */
    private $results;

    /**
     * @var integer
     */
    private $maxScore;

    /**
     * @var integer
     */
    private $numResults;

    /**
     * @param array   $results
     * @param integer $maxScore
     * @param integer $numResults
     */
    public function __construct(array $results, $maxScore, $numResults)
    {
        $this->results    = $results;
        $this->maxScore   = $maxScore;
        $this->numResults = $numResults;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return integer
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * @return integer
     */
    public function getNumResults()
    {
        return $this->numResults;
    }
}
