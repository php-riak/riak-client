<?php

namespace RiakClientFunctionalTest\Command\MapReduce;

/**
 * @group http
 * @group functional
 */
class SearchMapReduceHttpTest extends SearchMapReduceTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}