<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group proto
 * @group functional
 */
class IndexMapReduceProtoTest extends IndexMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}