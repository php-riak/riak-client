<?php

namespace RiakClientTest\Command\MapReduce\Input;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\KeyFilters;
use Riak\Client\Command\MapReduce\Input\BucketInput;

class BucketInputTest extends TestCase
{
    public function testIndexInput()
    {
        $namespace  = new RiakNamespace(null, 'bucket_name');
        $input      = new BucketInput($namespace);

        $this->assertNull($input->getFilters());
        $this->assertSame($namespace, $input->getNamespace());
        $this->assertEquals('{"bucket":"bucket_name","key_filters":[]}', json_encode($input));
    }

    public function testIndexInputWithType()
    {
        $namespace  = new RiakNamespace('bucket_type', 'bucket_name');
        $input      = new BucketInput($namespace);

        $this->assertNull($input->getFilters());
        $this->assertSame($namespace, $input->getNamespace());
        $this->assertEquals('{"bucket":["bucket_type","bucket_name"],"key_filters":[]}', json_encode($input));
    }

    public function testIndexInputWithFilters()
    {
        $filters    = KeyFilters::filter()->endsWith('Silva');
        $namespace  = new RiakNamespace('bucket_type', 'bucket_name');
        $input      = new BucketInput($namespace, $filters);

        $this->assertSame($namespace, $input->getNamespace());
        $this->assertSame($filters, $input->getFilters());
        $this->assertEquals('{"bucket":["bucket_type","bucket_name"],"key_filters":[["ends_with","Silva"]]}', json_encode($input));
    }
}