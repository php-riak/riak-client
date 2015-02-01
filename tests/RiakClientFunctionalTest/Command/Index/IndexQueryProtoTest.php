<?php

namespace RiakClientFunctionalTest\Command\Index;

/**
 * @group proto
 * @group functional
 */
class IndexQueryProtoTest extends IndexQueryTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakProtoClient();
    }
}