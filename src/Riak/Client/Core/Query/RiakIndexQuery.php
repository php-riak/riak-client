<?php

namespace Riak\Client\Core\Query;

/**
 * Riak presend riak secondary index query options.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakIndexQuery
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var string
     */
    protected $continuation;

    /**
     * @var mixed
     */
    protected $match;

    /**
     * @var mixed
     */
    protected $start;

    /**
     * @var mixed
     */
    protected $end;

    /**
     * @var integer
     */
    protected $maxResults;

    /**
     * @var boolean
     */
    protected $returnTerms;

    /**
     * @var boolean
     */
    protected $paginationSort;

    /**
     * @var string
     */
    protected $termFilter;

    /**
     * @var integer
     */
    protected $timeout;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param string                                $indexName
     * @param mixed                                 $start
     * @param mixed                                 $end
     */
    public function __construct(RiakNamespace $namespace = null, $indexName = null, $start = null, $end = null)
    {
        $this->namespace = $namespace;
        $this->indexName = $indexName;
        $this->start     = $start;
        $this->end       = $end;
    }

    /**
     * Set the continuation for this query.
     * The continuation is returned by a previous paginated query.
     *
     * @param string $continuation
     */
    public function setContinuation($continuation)
    {
        $this->continuation = $continuation;
    }

    /**
     * Set the maximum number of results returned by the query.
     *
     * @param integer $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }

    /**
     * Set whether to return the index keys set the Riak object keys.
     * Setting this to true will return both the index key and the Riak
     * object's key. The default is false (only to return the Riak object keys).
     *
     * @param boolean $returnTerms
     */
    public function setReturnTerms($returnTerms)
    {
        $this->returnTerms = $returnTerms;
    }

    /**
     * Set whether to sort the results of a non-paginated 2i query.
     * Setting this to true will sort the results in Riak before returning them.
     *
     * @param boolean $orderByKey
     */
    public function setPaginationSort($orderByKey)
    {
        $this->paginationSort = $orderByKey;
    }

    /**
     * Set the regex to filter result terms by for this query.
     *
     * @param string $filter
     */
    public function setTermFilter($filter)
    {
        $this->termFilter = $filter;
    }

    /**
     * Set the Riak-side timeout value.
     *
     * @param type $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     */
    public function setNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param string $indexName
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
    }

    /**
     * @param string $match
     */
    public function setMatch($match)
    {
        $this->match = $match;
    }

    /**
     * @param string $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @param string $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return \Riak\Client\Core\Query\RiakNamespace
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @return string
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * @return mixed
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return integer
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return boolean
     */
    public function getReturnTerms()
    {
        return $this->returnTerms;
    }

    /**
     * @return boolean
     */
    public function getPaginationSort()
    {
        return $this->paginationSort;
    }

    /**
     * @return string
     */
    public function getTermFilter()
    {
        return $this->termFilter;
    }

    /**
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }
}
