<?php

namespace RiakClientFunctionalTest\Command\Index;

/**
 * @group http
 * @group functional
 */
class IndexQueryHttpTest extends IndexQueryTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}