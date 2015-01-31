<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group html
 * @group functional
 */
class SearchHtmlTest extends SearchTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}