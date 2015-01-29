<?php

namespace Riak\Client\Command\Search\Builder;

use Riak\Client\Command\Search\StoreSchema;
use Riak\Client\Core\Query\Search\YokozunaSchema;

/**
 * Used to construct a StoreSchema command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreSchemaBuilder extends Builder
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
     * @param \Riak\Client\Core\Query\RiakLocation $schema
     *
     * @return \Riak\Client\Command\Search\Builder\StoreSchemaBuilder
     */
    public function withSchema(YokozunaSchema $schema)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Build a StoreSchema object
     *
     * @return \Riak\Client\Command\Search\StoreSchema
     */
    public function build()
    {
        return new StoreSchema($this->schema);
    }
}
