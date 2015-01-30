<?php

namespace Riak\Client\Command\Search\Builder;

use Riak\Client\Command\Search\Search;
use Riak\Client\Core\Query\RiakSearchQuery;

/**
 * Used to construct a Search command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class SearchBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakSearchQuery
     */
    private $query;

    /**
     * Init Search Builder
     */
    public function __construct()
    {
        $this->query = new RiakSearchQuery();
    }

    /**
     * Sorts all of the results by bucket key, or the search score, before the given rows are chosen.
     * This is useful when paginating to ensure the results are returned in a consistent order.
     *
     * @param string $presort
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withPresort($presort)
    {
        $this->query->setPresort($presort);

        return $this;
    }

    /**
     * Specify the starting result of the query.
     * Useful for pagination. The default is 0.
     *
     * @param integer $start
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withStart($start)
    {
        $this->query->setStart($start);

        return $this;
    }

    /**
     * Specify the maximum number of results to return.
     * Riak defaults to 10 if this is not set.
     *
     * @param integer $rows
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withNumRows($rows)
    {
        $this->query->setNumRows($rows);

        return $this;
    }

    /**
     * Filters the search by an additional query scoped to inline fields.
     *
     * @param string $query
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withFilterQuery($query)
    {
        $this->query->setFilterQuery($query);

        return $this;
    }

    /**
     * Sort the results on the specified field name.
     * Default is “none”, which causes the results to be sorted in descending order by score.
     *
     * @param string $field
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withSortField($field)
    {
        $this->query->setSortField($field);

        return $this;
    }

    /**
     * Only return the provided fields.
     * Filters the results to only contain the provided fields.
     *
     * @param array $fields
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withReturnFields(array $fields)
    {
        $this->query->setReturnFields($fields);

        return $this;
    }

    /**
     * @param string $field
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withReturnField($field)
    {
        $this->query->addReturnField($field);

        return $this;
    }

    /**
     * @param string $index
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withIndex($index)
    {
        $this->query->setIndex($index);

        return $this;
    }

    /**
     * @param string $query
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function withQuery($query)
    {
        $this->query->setQuery($query);

        return $this;
    }

    /**
     * Use the provided field as the default.
     * Overrides the “default_field” setting in the schema file.
     *
     * @param string $fieldName
     *
     * @return \Riak\Client\Core\Query\RiakSearchQuery
     */
    public function withDefaultField($fieldName)
    {
        $this->query->setDefaultField($fieldName);

        return $this;
    }

    /**
     * Set the default operation.
     * Allowed settings are either “and” or “or”.
     * Overrides the “default_op” setting in the schema file.
     *
     * @param string $op
     *
     * @return \Riak\Client\Core\Query\RiakSearchQuery
     */
    public function withDefaultOperation($op)
    {
        $this->query->setDefaultOperation($op);

        return $this;
    }

    /**
     * Build a FetchIndex object
     *
     * @return \Riak\Client\Command\Search\FetchIndex
     */
    public function build()
    {
        return new Search($this->query);
    }
}
