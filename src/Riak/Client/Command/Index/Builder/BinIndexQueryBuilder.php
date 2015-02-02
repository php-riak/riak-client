<?php

namespace Riak\Client\Command\Index\Builder;

use Riak\Client\Command\Index\BinIndexQuery;

/**
 * Used to construct a BinIndexQuery command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BinIndexQueryBuilder extends Builder
{
    /**
     * Set the regex to filter result terms by for this query.
     *
     * @param string $filter
     *
     * @return \Riak\Client\Command\Index\Builder\Builder
     */
    public function withTermFilter($filter)
    {
        $this->query->setTermFilter($filter);

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\Index\BinIndexQuery
     */
    public function build()
    {
        return new BinIndexQuery($this->query);
    }

    /**
     * {@inheritdoc}
     */
    protected function createFullIndexName($indexName)
    {
        return sprintf('%s_bin', $indexName);
    }
}
