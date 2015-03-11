<?php

namespace RiakClientTest\Command\MapReduce\Input;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\MapReduce\Input\IndexInput;
use Riak\Client\Command\MapReduce\Input\Index\MatchCriteria;

class IndexInputTest extends TestCase
{
    public function testIndexInput()
    {
        $indexName  = 'lang_bin';
        $criteria   = new MatchCriteria('php');
        $namespace  = new RiakNamespace(null, 'bucket_name');
        $input      = new IndexInput($namespace, $indexName, $criteria);

        $this->assertSame($criteria, $input->getCriteria());
        $this->assertSame($namespace, $input->getNamespace());
        $this->assertEquals($indexName, $input->getIndexName());
        $this->assertEquals('{"bucket":"bucket_name","index":"lang_bin","key":"php"}', json_encode($input));
    }

    public function testIndexInputWithType()
    {
        $indexName  = 'lang_bin';
        $criteria   = new MatchCriteria('php');
        $namespace  = new RiakNamespace('bucket_type', 'bucket_name');
        $input      = new IndexInput($namespace, $indexName, $criteria);

        $this->assertSame($criteria, $input->getCriteria());
        $this->assertSame($namespace, $input->getNamespace());
        $this->assertEquals($indexName, $input->getIndexName());
        $this->assertEquals('{"bucket":["bucket_type","bucket_name"],"index":"lang_bin","key":"php"}', json_encode($input));
    }
}