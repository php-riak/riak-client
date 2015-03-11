<?php

namespace RiakClientTest\Command\MapReduce\Phase;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Command\MapReduce\Phase\MapPhase;
use Riak\Client\Command\MapReduce\Phase\LinkPhase;
use Riak\Client\Command\MapReduce\Phase\ReducePhase;

class FunctionPhaseTest extends TestCase
{
    public function testMapPhase()
    {
        $function = new ErlangFunction('module', 'map_func');
        $phase    = new MapPhase($function, 'arg', true);

        $this->assertEquals('map', $phase->getPhaseName());
        $this->assertSame($function, $phase->getFunction());
        $this->assertEquals('arg', $phase->getArg());
        $this->assertTrue($phase->getKeepResult());
        $this->assertEquals('{"language":"erlang","module":"module","function":"map_func","keep":true,"arg":"arg"}', json_encode($phase));
    }

    public function testReducePhase()
    {
        $function = new ErlangFunction('module', 'reduce_func');
        $phase    = new ReducePhase($function, 'arg', true);

        $this->assertEquals('reduce', $phase->getPhaseName());
        $this->assertSame($function, $phase->getFunction());
        $this->assertEquals('arg', $phase->getArg());
        $this->assertTrue($phase->getKeepResult());
        $this->assertEquals('{"language":"erlang","module":"module","function":"reduce_func","keep":true,"arg":"arg"}', json_encode($phase));
    }

    public function testLinkPhase()
    {
        $phase = new LinkPhase('riak-bucket', 'tag', true);

        $this->assertEquals('link', $phase->getPhaseName());
        $this->assertSame('riak-bucket', $phase->getBucket());
        $this->assertSame('tag', $phase->getTag());
        $this->assertTrue($phase->getKeepResult());
        $this->assertEquals('{"bucket":"riak-bucket","tag":"tag","keep":true}', json_encode($phase));
    }
}