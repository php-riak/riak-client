<?php

namespace RiakClientTest\Command\MapReduce;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Command\MapReduce\Specification;
use Riak\Client\Command\MapReduce\Phase\MapPhase;
use Riak\Client\Command\MapReduce\Input\IndexInput;
use Riak\Client\Core\Query\Func\AnonymousJsFunction;
use Riak\Client\Command\MapReduce\Input\Index\MatchCriteria;

class SpecificationTest extends TestCase
{
    public function testJsonEncode()
    {
        $indexName  = 'lang_bin';
        $criteria   = new MatchCriteria('php');
        $namespace  = new RiakNamespace(null, 'bucket_name');
        $input      = new IndexInput($namespace, $indexName, $criteria);
        $spec       = new Specification($input, []);

        $spec->addPhase(new MapPhase(new AnonymousJsFunction('function(obj) { return obj; }')));

        $actual   = json_decode(json_encode($spec), true);
        $expected = [
            'inputs' => [
                'bucket' => 'bucket_name',
                'index'  => 'lang_bin',
                'key'    => 'php'
            ],
            'query' => [
                [
                    'map' => [
                        'language' => 'javascript',
                        'source'   => 'function(obj) { return obj; }',
                        'keep'     => false
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }

    public function testConstruct()
    {
        $indexName  = 'lang_bin';
        $criteria   = new MatchCriteria('php');
        $namespace  = new RiakNamespace(null, 'bucket_name');
        $input      = new IndexInput($namespace, $indexName, $criteria);
        $phases     = [new MapPhase(new ErlangFunction('module', 'map_func1'))];
        $spec       = new Specification($input, $phases, 120);

        $this->assertSame($input, $spec->getInput());
        $this->assertEquals($phases, $spec->getPhases());
        $this->assertEquals(120, $spec->getTimeout());

        $spec->setTimeout(220);

        $this->assertEquals(220, $spec->getTimeout());
    }
}