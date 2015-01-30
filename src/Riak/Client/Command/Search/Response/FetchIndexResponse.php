<?php

namespace Riak\Client\Command\Search\Response;

use Riak\Client\Core\Query\Search\YokozunaIndex;

/**
 * Fetch Index Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchIndexResponse extends Response
{
    /**
     * @var \Riak\Client\Core\Query\Search\YokozunaIndex
     */
    private $index;

    /**
     * @param \Riak\Client\Core\Query\Search\YokozunaIndex $schema
     */
    public function __construct(YokozunaIndex $schema = null)
    {
        $this->index = $schema;
    }

    /**
     * @return \Riak\Client\Core\Query\Search\YokozunaIndex
     */
    public function getIndex()
    {
        return $this->index;
    }
}
