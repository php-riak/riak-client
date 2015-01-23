<?php

namespace RiakClientFunctionalTest\Command\Kv;

/**
 * @group proto
 * @group functional
 */
class RiakUserMetaProtoTest extends RiakUserMetaTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}