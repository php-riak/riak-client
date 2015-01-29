<?php

namespace RiakClientFunctionalTest\Command\Search;

/**
 * @group proto
 * @group functional
 * @group deprecated
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