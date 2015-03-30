<?php

namespace RiakClientFunctionalTest\Command\Kv;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\RiakOption;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\Link\RiakLink;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class RiakLinkTest extends TestCase
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation
     */
    protected $location;

    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('default', 'buckets');
        $store     = StoreBucketProperties::builder()
            ->withAllowMulti(true)
            ->withNVal(3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);

        $this->key      = uniqid();
        $this->location = new RiakLocation($namespace, $this->key);
    }

    protected function tearDown()
    {
        if ($this->client) {
            $this->client->execute(DeleteValue::builder($this->location)
                ->build());
        }

        parent::tearDown();
    }

    public function testObjectWithLinks()
    {
        $object = new RiakObject();

        $object->setValue('{"name": "fabio"}');
        $object->setContentType('application/json');
        $object->addLink(new RiakLink(null, null, null));
        $object->addLink(new RiakLink(null, null, null));

        $object->getLinks()->get(0)->setBucket('bucket');
        $object->getLinks()->get(0)->setKey('first');
        $object->getLinks()->get(0)->setTag('foo');

        $object->getLinks()->get(1)->setBucket('bucket');
        $object->getLinks()->get(1)->setKey('second');
        $object->getLinks()->get(1)->setTag('bar');

        $store = StoreValue::builder($this->location, $object)
            ->withPw(1)
            ->withW(1)
            ->build();

        $fetch  = FetchValue::builder($this->location)
            ->withR(1)
            ->build();

        $this->client->execute($store);

        $result     = $this->client->execute($fetch);
        $riakObject = $result->getValue();
        $riakLinks  = $riakObject->getLinks();

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\Link\RiakLinkList', $riakLinks);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('{"name": "fabio"}', $riakObject->getValue());

        $this->assertCount(2, $riakLinks);
        $this->assertInstanceOf('Riak\Client\Core\Query\Link\RiakLink', $riakLinks[0]);
        $this->assertInstanceOf('Riak\Client\Core\Query\Link\RiakLink', $riakLinks[1]);
        $this->assertEquals('bucket', $riakLinks[0]->getBucket());
        $this->assertEquals('bucket', $riakLinks[1]->getBucket());
        $this->assertEquals('first', $riakLinks[0]->getKey());
        $this->assertEquals('second', $riakLinks[1]->getKey());
        $this->assertEquals('foo', $riakLinks[0]->getTag());
        $this->assertEquals('bar', $riakLinks[1]->getTag());

        $this->client->execute(DeleteValue::builder($this->location)
            ->build());
    }
}