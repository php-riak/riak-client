<?php

namespace RiakClientTest\Command\Bucket;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Bucket\ListBuckets;

class ListBucketsTest extends TestCase
{
    /**
     * @var string
     */
    private $type;

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
        $this->node      = new RiakNode($this->adapter);
        $this->type      = 'type';
        $this->client    = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testBuildCommand()
    {
        $builder = ListBuckets::builder()
            ->withBucketType($this->type)
            ->withTimeout(3600);

        $this->assertInstanceOf('Riak\Client\Command\Bucket\ListBuckets', $builder->build());
    }
}