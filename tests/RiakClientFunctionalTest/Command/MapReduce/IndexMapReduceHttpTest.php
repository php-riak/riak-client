<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group http
 * @group functional
 */
class IndexMapReduceHttpTest extends IndexMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}