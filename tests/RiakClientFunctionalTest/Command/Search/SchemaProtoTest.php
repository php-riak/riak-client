<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group proto
 * @group functional
 */
class SchemaProtoTest extends SchemaTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}