<?php

namespace Riak\Client\Command\Search\Response;

use Riak\Client\Core\Query\Search\YokozunaSchema;

/**
 * Fetch Schema Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSchemaResponse extends Response
{
    /**
     * @var \Riak\Client\Core\Query\Search\YokozunaSchema
     */
    private $schema;

    /**
     * @param \Riak\Client\Core\Query\Search\YokozunaSchema $schema
     */
    public function __construct(YokozunaSchema $schema = null)
    {
        $this->schema = $schema;
    }

    /**
     * @return \Riak\Client\Core\Query\Search\YokozunaSchema
     */
    public function getSchema()
    {
        return $this->schema;
    }
}
