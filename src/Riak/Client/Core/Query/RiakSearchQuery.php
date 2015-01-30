<?php

namespace Riak\Client\Core\Query;

/**
 * Riak presend a riak search options.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakSearchQuery
{
    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $query;

    /**
     * @var integer
     */
    private $start;

    /**
     * @var integer
     */
    private $numRows;

    /**
     * @var string
     */
    private $presort;

    /**
     * @var string
     */
    private $filterQuery;

    /**
     * @var string
     */
    private $sortField;

    /**
     * @var string
     */
    private $defaultField;

    /**
     * @var string
     */
    private $defaultOperation;

    /**
     * @var array
     */
    private $returnFields;

    /**
     * @param string $index
     * @param string $query
     */
    public function __construct($index = null, $query = null)
    {
        $this->index = $index;
        $this->query = $query;
    }

    /**
     * Sorts all of the results by bucket key, or the search score, before the given rows are chosen.
     * This is useful when paginating to ensure the results are returned in a consistent order.
     *
     * 'score' or 'key'
     *
     * @param string $presort
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setPresort($presort)
    {
        $this->presort = $presort;
    }

    /**
     * Specify the starting result of the query.
     * Useful for pagination. The default is 0.
     *
     * @param integer $start
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Specify the maximum number of results to return.
     * Riak defaults to 10 if this is not set.
     *
     * @param integer $rows
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setNumRows($rows)
    {
        $this->numRows = $rows;
    }

    /**
     * Filters the search by an additional query scoped to inline fields.
     *
     * @param string $filterQuery
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setFilterQuery($filterQuery)
    {
        $this->filterQuery = $filterQuery;
    }

    /**
     * Sort the results on the specified field name.
     * Default is “none”, which causes the results to be sorted in descending order by score.
     *
     * @param string $field
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setSortField($field)
    {
        $this->sortField = $field;
    }

    /**
     * Only return the provided fields.
     * Filters the results to only contain the provided fields.
     *
     * @param array $fields
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setReturnFields(array $fields)
    {
        $this->returnFields = $fields;
    }

    /**
     * Add a return fields to the result.
     *
     * @param string $field
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function addReturnField($field)
    {
        $this->returnFields[] = $field;
    }

    /**
     * @param string $index
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * Use the provided field as the default.
     * Overrides the “default_field” setting in the schema file.
     *
     * @param string $fieldName
     *
     * @return \Riak\Client\Core\Query\RiakSearchQuery
     */
    public function setDefaultField($fieldName)
    {
        $this->defaultField = $fieldName;

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
    public function setDefaultOperation($op)
    {
        $this->defaultOperation = $op;

        return $this;
    }

    /**
     * @param string $query
     *
     * @return \Riak\Client\Command\Search\Search
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return integer
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return integer
     */
    public function getNumRows()
    {
        return $this->numRows;
    }

    /**
     * @return integer
     */
    public function getPresort()
    {
        return $this->presort;
    }

    /**
     * @return string
     */
    public function getFilterQuery()
    {
        return $this->filterQuery;
    }

    /**
     * @return string
     */
    public function getSortField()
    {
        return $this->sortField;
    }

    /**
     * @return array
     */
    public function getReturnFields()
    {
        return $this->returnFields;
    }

    /**
     * @return string
     */
    public function getDefaultField()
    {
        return $this->defaultField;
    }

    /**
     * @return string
     */
    public function getDefaultOperation()
    {
        return $this->defaultOperation;
    }
}
