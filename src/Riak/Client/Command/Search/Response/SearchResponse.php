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
     * @var \Riak\Client\Core\Query\RiakList
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
     * @param \Riak\Client\Core\Query\RiakList $results
     * @param integer                          $maxScore
     * @param integer                          $numResults
     */
    public function __construct($results, $maxScore, $numResults)
    {
        $this->results    = $results;
        $this->maxScore   = $maxScore;
        $this->numResults = $numResults;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakList
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
