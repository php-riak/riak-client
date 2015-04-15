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
     * Returns the entire list of results from the search query.
     *
     * @return array A list containing all the result sets
     */
    public function getAllResults()
    {
        return $this->results;
    }

    /**
     * Returns the first entry of each element from the search query result.
     * If your result contains a multi-valued field you might whant to use  @see SearchResponse::getAllResults()
     *
     * @return array
     */
    public function getSingleResults()
    {
        return array_map(function ($result) {
            return array_map('reset', $result);
        }, $this->results);
    }

    /**
     * Returns the max score from the search query.
     *
     * @return integer
     */
    public function getMaxScore()
    {
        return $this->maxScore;
    }

    /**
     * Returns the number of results from the search query.
     *
     * @return integer
     */
    public function getNumResults()
    {
        return $this->numResults;
    }
}
