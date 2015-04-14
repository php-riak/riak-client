<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group proto
 * @group functional
 */
class DataTypeSearchProtoTest extends DataTypeSearchTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}