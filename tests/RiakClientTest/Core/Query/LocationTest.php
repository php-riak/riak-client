<?php

namespace RiakClientTest\Core\Query;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;

class LocationTest extends TestCase
{
    public function testLocationAndLocation()
    {
        $namaspace1 = new RiakNamespace('type-1', 'bucket-1');
        $namaspace2 = new RiakNamespace('type-2', 'bucket-2');
        $location   = new RiakLocation($namaspace1, 'key-1');

        $this->assertEquals('key-1', $location->getKey());
        $this->assertSame($namaspace1, $location->getNamespace());
        $this->assertEquals('type-1', $location->getNamespace()->getBucketType());
        $this->assertEquals('bucket-1', $location->getNamespace()->getBucketName());

        $location->setKey('key-2');
        $location->setNamespace($namaspace2);
        $this->assertEquals('key-2', $location->getKey());
        $this->assertSame($namaspace2, $location->getNamespace());
        $this->assertEquals('type-2', $location->getNamespace()->getBucketType());
        $this->assertEquals('bucket-2', $location->getNamespace()->getBucketName());
    }
}