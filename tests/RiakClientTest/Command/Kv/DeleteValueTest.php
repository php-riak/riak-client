<?php

namespace RiakClientTest\Command\Kv;

use RiakClientTest\TestCase;
use Riak\Client\Core\RiakNode;
use Riak\Client\RiakClientBuilder;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Message\Kv\DeleteResponse;

class DeleteValueTest extends TestCase
{
    private $vClock;
    private $location;
    private $client;
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $builder = new RiakClientBuilder();

        $this->location = new RiakLocation(new RiakNamespace('type', 'bucket'), 'key');
        $this->adapter  = $this->getMock('Riak\Client\Core\RiakTransport');
        $this->vClock   = $this->getMock('Riak\Client\Core\Query\VClock', [], ['hash']);
        $this->node     = new RiakNode($this->adapter);
        $this->client   = $builder
            ->withNode($this->node)
            ->build();
    }

    public function testDelete()
    {
        $deleteResponse = new DeleteResponse();
        $command        = DeleteValue::builder()
            ->withLocation($this->location)
            ->withVClock($this->vClock)
            ->withPr(1)
            ->withPw(1)
            ->withRw(2)
            ->withDw(2)
            ->withW(3)
            ->withR(3)
            ->build();

        $this->adapter->expects($this->once())
            ->method('send')
            ->will($this->returnValue($deleteResponse));

        $result = $this->client->execute($command);

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\DeleteValueResponse', $result);
    }
}