<?php

namespace Riak\Client\Command\Index\Builder;

use Riak\Client\Command\Index\IntIndexQuery;

/**
 * Used to construct a IntIndexQuery command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class IntIndexQueryBuilder extends Builder
{
    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\Index\IntIndexQuery
     */
    public function build()
    {
        return new IntIndexQuery($this->query);
    }

    /**
     * {@inheritdoc}
     */
    protected function createFullIndexName($indexName)
    {
        return sprintf('%s_int', $indexName);
    }
}
