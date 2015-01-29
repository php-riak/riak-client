<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group proto
 * @group functional
 */
class IndexProtoTest extends IndexTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}