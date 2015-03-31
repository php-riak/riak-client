<?php

namespace RiakClientFunctionalTest\Command\Kv;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\Index\RiakIndexBin;
use Riak\Client\Core\Query\Index\RiakIndexInt;
use Riak\Client\Core\Query\Index\RiakIndexList;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class RiakIndexTest extends TestCase
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

    public function testObjectWithIndexes()
    {
        $key        = uniqid();
        $object     = new RiakObject();
        $indexes    = new RiakIndexList([]);
        $location   = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $indexes['key']   = new RiakIndexInt('key');
        $indexes['email'] = new RiakIndexBin('email');

        $indexes['key']->addValue(123);
        $indexes['email']->addValue('fabio.bat.silva@gmail.com');

        $object->setContentType('application/json');
        $object->setValue('{"name": "fabio"}');
        $object->setIndexes($indexes);

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

        $result      = $this->client->execute($fetch);
        $riakObject  = $result->getValue();
        $riakIndexes = $riakObject->getIndexes();

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\Index\RiakIndexList', $riakIndexes);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('{"name": "fabio"}', $riakObject->getValue());

        $this->assertCount(2, $riakIndexes);
        $this->assertTrue(isset($riakIndexes['key']));
        $this->assertTrue(isset($riakIndexes['email']));

        $this->assertInstanceOf('Riak\Client\Core\Query\Index\RiakIndexInt', $riakIndexes['key']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Index\RiakIndexBin', $riakIndexes['email']);

        $this->assertEquals('key', $riakIndexes['key']->getName());
        $this->assertEquals('email', $riakIndexes['email']->getName());

        $this->assertEquals([123], $riakIndexes['key']->getValues());
        $this->assertEquals(['fabio.bat.silva@gmail.com'], $riakIndexes['email']->getValues());

        $this->client->execute(DeleteValue::builder($location)
            ->build());
    }

    public function testSiblingsWithIndexes()
    {
        $key        = uniqid();
        $object1    = new RiakObject();
        $object2    = new RiakObject();
        $location   = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $object1->addIndex(new RiakIndexBin('group', ['guest']));
        $object1->setContentType('application/json');
        $object1->setValue('{"name": "fabio"}');

        $object2->addIndex(new RiakIndexBin('group', ['admin']));
        $object2->setContentType('application/json');
        $object2->setValue('{"name": "fabio"}');

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

        $riakObject1  = $result->getValues()->offsetGet(0);
        $riakObject2  = $result->getValues()->offsetGet(1);
        $riakIndexes1 = $riakObject1->getIndexes();
        $riakIndexes2 = $riakObject2->getIndexes();

        $this->assertInstanceOf('Riak\Client\Core\Query\Index\RiakIndexList', $riakIndexes1);
        $this->assertInstanceOf('Riak\Client\Core\Query\Index\RiakIndexList', $riakIndexes2);

        $this->assertCount(1, $riakIndexes1);
        $this->assertTrue(isset($riakIndexes1['group']));
        $this->assertTrue(isset($riakIndexes2['group']));
        $this->assertInstanceOf('Riak\Client\Core\Query\Index\RiakIndexBin', $riakIndexes1['group']);
        $this->assertInstanceOf('Riak\Client\Core\Query\Index\RiakIndexBin', $riakIndexes2['group']);

        $this->assertEquals('group', $riakIndexes1['group']->getName());
        $this->assertEquals('group', $riakIndexes2['group']->getName());
        $this->assertEquals(['guest'], $riakIndexes1['group']->getValues());
        $this->assertEquals(['admin'], $riakIndexes2['group']->getValues());

        $this->client->execute(DeleteValue::builder($location)
            ->build());
    }
}