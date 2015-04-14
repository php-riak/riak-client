<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group http
 * @group functional
 */
class DataTypeSearchHttpTest extends DataTypeSearchTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}