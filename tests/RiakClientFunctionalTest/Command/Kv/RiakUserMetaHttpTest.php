<?php

namespace RiakClientFunctionalTest\Command\Kv;

/**
 * @group http
 * @group functional
 */
class RiakUserMetaHttpTest extends RiakUserMetaTest
{
    /**
     * {@inheritdoc}
     */
    protected function createClient()
    {
        return $this->createRiakHttpClient();
    }
}