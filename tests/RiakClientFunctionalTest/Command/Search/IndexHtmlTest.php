<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group html
 * @group functional
 * @group deprecated
 */
class IndexHtmlTest extends IndexTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}