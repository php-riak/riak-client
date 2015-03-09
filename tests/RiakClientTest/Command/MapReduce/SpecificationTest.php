<?php

namespace RiakClientTest\Command\MapReduce;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\RiakNamespace;
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
}