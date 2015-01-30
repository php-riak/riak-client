<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group proto
 * @group functional
 */
class SearchProtoTest extends SearchTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}