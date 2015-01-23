<?php

namespace RiakClientFunctionalTest\Command\DataType;

/**
 * @group proto
 * @group functional
 */
class CounterProtoTest extends CounterTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}