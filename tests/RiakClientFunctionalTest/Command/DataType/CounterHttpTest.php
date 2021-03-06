<?php

namespace RiakClientFunctionalTest\Command\DataType;

/**
 * @group http
 * @group functional
 */
class CounterHttpTest extends CounterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}