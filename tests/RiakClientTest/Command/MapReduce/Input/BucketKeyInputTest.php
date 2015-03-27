<?php

namespace RiakClientTest\Command\MapReduce\Input;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\Input\BucketKeyInput;

class BucketKeyInputTest extends TestCase
{
    public function testBucketKeyInput()
    {
        $namespace  = new RiakNamespace(null, 'bucket');
        $location1  = new RiakLocation($namespace, 'key1');
        $location2  = new RiakLocation($namespace, 'key2');
        $input      = new BucketKeyInput();

        $input->addLocation($location1);
        $input->addLocation($location2, 'data');

        $inputs = $input->getInputs();

        $this->assertCount(2, $inputs);
        $this->assertNull($inputs[0]->getData());
        $this->assertEquals('data' ,$inputs[1]->getData());
        $this->assertSame($location2, $inputs[1]->getLocation());
        $this->assertSame($location1, $inputs[0]->getLocation());
        $this->assertSame($location2, $inputs[1]->getLocation());
        $this->assertEquals('[["bucket","key1",""],["bucket","key2","data"]]', json_encode($input));
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Input\BucketKey\Input', $inputs[0]);
        $this->assertInstanceOf('Riak\Client\Command\MapReduce\Input\BucketKey\Input', $inputs[1]);
    }

    public function testBucketKeyInputWithType()
    {
        $namespace  = new RiakNamespace('type', 'bucket');
        $location1  = new RiakLocation($namespace, 'key1');
        $location2  = new RiakLocation($namespace, 'key2');
        $input      = new BucketKeyInput();

        $input->addLocation($location1);
        $input->addLocation($location2, 'data');

        $this->assertEquals('[["bucket","key1","","type"],["bucket","key2","data","type"]]', json_encode($input));
    }
}