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
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Core\Query\Meta\RiakUsermeta;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class RiakUserMetaTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('default', 'bucket');
        $store     = StoreBucketProperties::builder()
            ->withAllowMulti(true)
            ->withNVal(3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);
    }

    public function testObjectWithUserMeta()
    {
        $key        = uniqid();
        $object     = new RiakObject();
        $meta       = new RiakUsermeta();
        $location   = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $meta['key']    = 'other';
        $meta['meta']   = 'content';
        $meta['remove'] = 'other';

        $meta->remove('remove');
        $meta->put('key', 'value');

        $object->setContentType('application/json');
        $object->setValue('{"name": "fabio"}');
        $object->setUserMeta($meta);

        $store = StoreValue::builder($location, $object)
            ->withReturnBody(true)
            ->withPw(1)
            ->withW(1)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withNotFoundOk(true)
            ->withR(1)
            ->build();

        $this->client->execute($store);

        $result     = $this->client->execute($fetch);
        $riakObject = $result->getValue();
        $riakMeta   = $riakObject->getUserMeta();

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\Meta\RiakUserMeta', $riakMeta);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('{"name": "fabio"}', $riakObject->getValue());

        $this->assertCount(2, $riakMeta);
        $this->assertTrue(isset($riakMeta['key']));
        $this->assertTrue(isset($riakMeta['meta']));
        $this->assertEquals('value', $riakMeta['key']);
        $this->assertEquals('content', $riakMeta['meta']);
        $this->assertEquals('value', $riakMeta->get('key'));
        $this->assertEquals('content', $riakMeta->get('meta'));

        $this->client->execute(DeleteValue::builder($location)
            ->build());
    }

    public function testSiblingsWithUserMeta()
    {
        $key      = uniqid();
        $object1  = new RiakObject();
        $object2  = new RiakObject();
        $location = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $object1->setContentType('application/json');
        $object1->setValue('{"name": "fabio"}');
        $object1->addMeta('group', 'guest');

        $object2->setContentType('application/json');
        $object2->setValue('{"name": "fabio"}');
        $object2->addMeta('group', 'admin');

        $this->client->execute(StoreValue::builder($location, $object1)
            ->withW(3)
            ->build());

        $this->client->execute(StoreValue::builder($location, $object2)
            ->withW(3)
            ->build());

        $result = $this->client->execute(FetchValue::builder($location)
            ->withNotFoundOk(true)
            ->withR(1)
            ->build());

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertCount(2, $result->getValues());

        $riakObject1 = $result->getValues()->offsetGet(0);
        $riakObject2 = $result->getValues()->offsetGet(1);
        $riakMeta1   = $riakObject1->getUserMeta();
        $riakMeta2   = $riakObject2->getUserMeta();

        $this->assertInstanceOf('Riak\Client\Core\Query\Meta\RiakUserMeta', $riakMeta1);
        $this->assertInstanceOf('Riak\Client\Core\Query\Meta\RiakUserMeta', $riakMeta2);

        $this->assertCount(1, $riakMeta1);
        $this->assertTrue(isset($riakMeta1['group']));
        $this->assertTrue(isset($riakMeta2['group']));
        $this->assertEquals('guest', $riakMeta1['group']);
        $this->assertEquals('admin', $riakMeta2['group']);

        $this->client->execute(DeleteValue::builder($location)
            ->build());
    }
}