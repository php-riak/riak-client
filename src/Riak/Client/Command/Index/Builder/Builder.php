<?php

namespace Riak\Client\Command\Index\Builder;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\RiakIndexQuery;

/**
 * Used to construct a 2i query command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakIndexQuery
     */
    protected $query;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     * @param string                                $indexName
     * @param mixed                                 $start
     * @param mixed                                 $end
     */
    public function __construct(RiakNamespace $namespace = null, $indexName = null, $start = null, $end = null)
    {
        $fullIndexName = $indexName ? $this->createFullIndexName($indexName) : null;
        $this->query   = new RiakIndexQuery($namespace, $fullIndexName, $start, $end);
    }

    /**
     * Set the continuation for this query.
     * The continuation is returned by a previous paginated query.
     *
     * @param string $continuation
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withContinuation($continuation)
    {
        $this->query->setContinuation($continuation);

        return $this;
    }

    /**
     * Set the maximum number of results returned by the query.
     *
     * @param integer $maxResults
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withMaxResults($maxResults)
    {
        $this->query->setMaxResults($maxResults);

        return $this;
    }

    /**
     * Set whether to return the index keys with the Riak object keys.
     * Setting this to true will return both the index key and the Riak
     * object's key. The default is false (only to return the Riak object keys).
     *
     * @param boolean $returnTerms
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withReturnTerms($returnTerms)
    {
        $this->query->setReturnTerms($returnTerms);

        return $this;
    }

    /**
     * Set whether to sort the results of a non-paginated 2i query.
     * Setting this to true will sort the results in Riak before returning them.
     *
     * @param boolean $orderByKey
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withPaginationSort($orderByKey)
    {
        $this->query->setPaginationSort($orderByKey);

        return $this;
    }

    /**
     * Set the Riak-side timeout value.
     *
     * @param type $timeout
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withTimeout($timeout)
    {
        $this->query->setTimeout($timeout);

        return $this;
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withNamespace(RiakNamespace $namespace)
    {
        $this->query->setNamespace($namespace);

        return $this;
    }

    /**
     * @param string $indexName
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withIndexName($indexName)
    {
        $this->query->setIndexName($this->createFullIndexName($indexName));

        return $this;
    }

    /**
     * @param string $match
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withMatch($match)
    {
        $this->query->setMatch($match);

        return $this;
    }

    /**
     * @param string $start
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withStart($start)
    {
        $this->query->setStart($start);

        return $this;
    }

    /**
     * @param string $end
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withEnd($end)
    {
        $this->query->setEnd($end);

        return $this;
    }

    /**
     * @param string $indexName
     *
     * @return string
     */
    abstract protected function createFullIndexName($indexName);

    /**
     * Build a riak command object
     *
     * @return \Riak\Client\Command\Index\IndexQuery
     */
    abstract public function build();
}
