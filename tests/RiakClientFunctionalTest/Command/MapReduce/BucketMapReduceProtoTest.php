<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group proto
 * @group functional
 */
class BucketMapReduceProtoTest extends BucketMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}