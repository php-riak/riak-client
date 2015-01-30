<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group http
 * @group functional
 */
class SchemaHttpTest extends SchemaTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}