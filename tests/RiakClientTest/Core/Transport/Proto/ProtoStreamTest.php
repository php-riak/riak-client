<?php

namespace RiakClientTest\Core\Transport\Proto;

use Riak\Client\Core\Transport\Proto\ProtoStream;
use RiakClientTest\TestCase;

class ProtoStreamTest extends TestCase
{
    public function testToString()
    {
        $stream = new ProtoStream(null);
        $string = $stream->__toString();

        $this->assertEquals('', $string);
    }
}