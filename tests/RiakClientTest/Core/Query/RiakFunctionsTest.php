<?php

namespace RiakClientTest\Core\Query;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Core\Query\Func\NamedJsFunction;
use Riak\Client\Core\Query\Func\StoredJsFunction;
use Riak\Client\Core\Query\Func\AnonymousJsFunction;

class RiakFunctionsTest extends TestCase
{
    public function testAnonymousJsFunction()
    {
        $source   = 'function(value) { return [1]; }';
        $function = new AnonymousJsFunction('function(value) { return [1]; }');

        $this->assertEquals($source, $function->getSource());
        $this->assertEquals('{"language":"javascript","source":"function(value) { return [1]; }"}', json_encode($function));
    }

    public function testErlangFunction()
    {
        $function = new ErlangFunction('riak_kv_mapreduce', 'reduce_sum');

        $this->assertEquals('riak_kv_mapreduce', $function->getModule());
        $this->assertEquals('reduce_sum', $function->getFunction());
        $this->assertEquals('{"language":"erlang","module":"riak_kv_mapreduce","function":"reduce_sum"}', json_encode($function));
    }

    public function testNamedJsFunction()
    {
        $function = new NamedJsFunction('function_name');

        $this->assertEquals('function_name', $function->getName());
        $this->assertEquals('{"language":"javascript","name":"function_name"}', json_encode($function));
    }

    public function testStoredJsFunction()
    {
        $function = new StoredJsFunction('riak-bucket', 'riak-key');

        $this->assertEquals('riak-bucket', $function->getBucket());
        $this->assertEquals('riak-key', $function->getKey());
        $this->assertEquals('{"language":"javascript","bucket":"riak-bucket","key":"riak-key"}', json_encode($function));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid function : {"UNKNOWN_PROPERTY":"UNKNOWN_VALUE"}
     */
    public function testNamedJsFunctionInvalidArgumentException()
    {
        NamedJsFunction::createFromArray(['UNKNOWN_PROPERTY' => 'UNKNOWN_VALUE']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid function : {"UNKNOWN_PROPERTY":"UNKNOWN_VALUE"}
     */
    public function testErlangFunctionInvalidArgumentException()
    {
        ErlangFunction::createFromArray(['UNKNOWN_PROPERTY' => 'UNKNOWN_VALUE']);
    }
}