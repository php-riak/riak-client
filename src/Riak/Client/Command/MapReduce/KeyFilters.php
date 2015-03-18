<?php

namespace Riak\Client\Command\MapReduce;

use JsonSerializable;

/**
 * Filter class for building up lists of key filters
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class KeyFilters implements JsonSerializable
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        $underscore = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $name);
        $filter     = strtolower($underscore);

        $this->appendComparison($filter, $arguments);

        return $this;
    }

    /**
     * @param array $set
     *
     * @return \Riak\Client\Command\MapReduce\Filter\KeyFilter
     */
    public function setMember(array $set)
    {
        return $this->appendComparison('set_member', $set);
    }

    /**
     * @param string $filter
     * @param array  $arguments
     *
     * @return \Riak\Client\Command\MapReduce\Filter\KeyFilter
     */
    private function appendComparison($filter, array $arguments)
    {
        $this->filters[] = array_merge([$filter], $arguments);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->filters;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\Filter\KeyFilter
     */
    public static function filter()
    {
        return new self();
    }
}
