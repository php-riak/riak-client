<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group proto
 * @group functional
 */
class BucketKeyMapReduceProtoTest extends BucketKeyMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}