<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group http
 * @group functional
 * @group deprecated
 */
class IndexHttpTest extends IndexTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}