<?php

namespace RiakClientTest\Core\Message;

use RiakClientTest\TestCase;
use Riak\Client\Core\Message\Kv\GetRequest;

class MessageTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown property 'UNKNOWN_PROPERTY' on 'Riak\Client\Core\Message\Kv\GetRequest'
     */
    public function testUnknownPropertyException()
    {
        $request = new GetRequest;

        $request->UNKNOWN_PROPERTY = 'invalid';
    }
}