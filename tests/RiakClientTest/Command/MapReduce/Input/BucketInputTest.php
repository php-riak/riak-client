<?php

namespace RiakClientTest\Command\MapReduce\Input;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\Input\BucketInput;

class BucketInputTest extends TestCase
{
    public function testIndexInput()
    {
        $namespace  = new RiakNamespace(null, 'bucket_name');
        $input      = new BucketInput($namespace, []);

        $this->assertSame($namespace, $input->getNamespace());
        $this->assertEquals([], $input->getFilters());
        $this->assertEquals('{"bucket":"bucket_name","key_filters":[]}', json_encode($input));
    }

    public function testIndexInputWithType()
    {
        $namespace  = new RiakNamespace('bucket_type', 'bucket_name');
        $input      = new BucketInput($namespace, []);

        $this->assertSame($namespace, $input->getNamespace());
        $this->assertEquals([], $input->getFilters());
        $this->assertEquals('{"bucket":["bucket_type","bucket_name"],"key_filters":[]}', json_encode($input));
    }
}