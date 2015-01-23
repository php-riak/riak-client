<?php

namespace Riak\Client\Core\Query\Index;

use Riak\Client\Core\Query\RiakList;

/**
 * Represents list of riak indexes.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakIndexList extends RiakList
{
    /**
     * @param \Riak\Client\Core\Query\Index\RiakIndex[] $list
     */
    public function __construct(array $list = [])
    {
        parent::__construct([]);

        array_walk($list, [$this, 'addIndex']);
    }

    /**
     * @param \Riak\Client\Core\Query\Index\RiakIndex $index
     */
    public function addIndex(RiakIndex $index)
    {
        $this->list[$index->getName()] = $index;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->addIndex($value);
    }

    /**
     * @return array
     */
    public function toFullNameArray()
    {
        $values = [];

        foreach ($this->list as $index) {
            $values[$index->getFullName()] = $index->getValues();
        }

        return $values;
    }
}
