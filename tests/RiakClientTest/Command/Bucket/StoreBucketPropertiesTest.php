<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\Func\ErlangFunction;
use Riak\Client\Command\Bucket\StoreBucketProperties;

class StoreBucketPropertiesTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var \Riak\Client\RiakClient
     */
    private $client;

    /**
     * @var \Riak\Client\Core\RiakTransport
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->adapter   = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->namespace = new RiakNamespace('type', 'bucket');
        $this->node      = new RiakNode($this->adapter);
        $this->client    = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommand()
    {
        $builder = StoreBucketProperties::builder($this->namespace)
            ->withLinkwalkFunction(new ErlangFunction('module_linkwalk', 'function'))
            ->withChashkeyFunction(new ErlangFunction('module_chashkey', 'function'))
            ->withPostcommitHook(new ErlangFunction('module_postcommit', 'function'))
            ->withPrecommitHook(new ErlangFunction('module_precommit', 'function'))
            ->withNamespace($this->namespace)
            ->withSearchIndex('search-index')
            ->withBackend('backend')
            ->withLastWriteWins(true)
            ->withBasicQuorum(true)
            ->withNotFoundOk(true)
            ->withAllowMulti(true)
            ->withSmallVClock(4444)
            ->withYoungVClock(3333)
            ->withOldVClock(2222)
            ->withBigVClock(11111)
            ->withNVal(5)
            ->withRw(2)
            ->withDw(2)
            ->withPr(1)
            ->withPw(1)
            ->withW(3)
            ->withR(3);

        $this->assertInstanceOf('Riak\Client\Command\Bucket\StoreBucketProperties', $builder->build());
    }
}