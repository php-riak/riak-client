<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group proto
 * @group functional
 */
class SearchMapReduceProtoTest extends SearchMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}