<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group http
 * @group functional
 */
class SearchHttpTest extends SearchTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}