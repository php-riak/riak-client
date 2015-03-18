<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group http
 * @group functional
 */
class BucketMapReduceHttpTest extends BucketMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}