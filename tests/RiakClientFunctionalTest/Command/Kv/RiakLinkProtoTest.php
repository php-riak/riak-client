<?php

namespace RiakClientFunctionalTest\Command\Kv;

/**
 * @group proto
 * @group functional
 */
class RiakLinkProtoTest extends RiakLinkTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}