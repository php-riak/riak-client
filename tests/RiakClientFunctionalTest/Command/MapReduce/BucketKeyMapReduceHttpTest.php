<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group http
 * @group functional
 */
class BucketKeyMapReduceHttpTest extends BucketKeyMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}