<?php

namespace RiakClientFunctionalTest\Command\DataType;

/**
 * @group http
 * @group functional
 */
class MapHttpTest extends MapTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}