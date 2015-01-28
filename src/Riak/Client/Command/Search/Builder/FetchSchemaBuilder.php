<?php

namespace Riak\Client\Command\Search\Builder;

use Riak\Client\Command\Search\FetchSchema;

/**
 * Used to construct a FetchSchema command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchSchemaBuilder extends Builder
{
    /**
     * @var string
     */
    private $schemaName;

    /**
     * @param string $schemaName
     */
    public function __construct($schemaName = null)
    {
        $this->schemaName = $schemaName;
    }

    /**
     * @param string $schemaName
     *
     * @return \Riak\Client\Command\Search\Builder\FetchSchemaBuilder
     */
    public function withSchemaName($schemaName)
    {
        $this->schemaName = $schemaName;

        return $this;
    }

    /**
     * Build a FetchSchema object
     *
     * @return \Riak\Client\Command\Search\FetchSchema
     */
    public function build()
    {
        return new FetchSchema($this->schemaName);
    }
}
