<?php

namespace RiakClientFunctionalTest\Command\Kv;

use Riak\Client\RiakOption;
use Riak\Client\Command\Kv\ListKeys;
use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use RiakClientFixture\Domain\SimpleObject;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class RiakObjectTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->client->execute(StoreBucketProperties::builder()
            ->withNamespace(new RiakNamespace('default', 'bucket'))
            ->withAllowMulti(true)
            ->withNVal(3)
            ->build());
    }

    public function testStoreAndFetchSingleValue()
    {
        $key      = uniqid();
        $object   = new RiakObject();
        $location = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $object->setValue('[1,1,1]');
        $object->setContentType('application/json');

        $store = StoreValue::builder($location, $object)
            ->withW(RiakOption::QUORUM)
            ->withPw(RiakOption::ONE)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withNotFoundOk(true)
            ->withR(RiakOption::QUORUM)
            ->build();

        $this->client->execute($store);

        $result     = $this->client->execute($fetch);
        $riakObject = $result->getValue();

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('[1,1,1]', $riakObject->getValue());

        $this->client->execute(DeleteValue::builder($location)
            ->build());
    }

    public function testStoreAndFetchValueWithSiblings()
    {
        $key      = uniqid();
        $object1  = new RiakObject();
        $object2  = new RiakObject();
        $location = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $object1->setValue('[1,1,1]');
        $object2->setValue('[2,2,2]');
        $object1->setContentType('application/json');
        $object2->setContentType('application/json');

        $store1 = StoreValue::builder($location, $object1)
            ->withPw(1)
            ->withW(1)
            ->build();

        $store2 = StoreValue::builder($location, $object2)
            ->withW(1)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withNotFoundOk(true)
            ->withR(1)
            ->build();

        $delete  = DeleteValue::builder($location)
            ->build();

        $resultFetch1  = $this->client->execute($fetch);
        $resultStore1  = $this->client->execute($store1);
        $resultStore2  = $this->client->execute($store2);
        $resultFetch2  = $this->client->execute($fetch);
        $resultDelete  = $this->client->execute($delete);
        $resultFetch3  = $this->client->execute($fetch);

        $this->assertTrue($resultFetch1->getNotFound());
        $this->assertFalse($resultFetch2->getNotFound());
        $this->assertTrue($resultFetch3->getNotFound());

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\StoreValueResponse', $resultStore1);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\StoreValueResponse', $resultStore2);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $resultFetch2);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $resultFetch3);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\DeleteValueResponse', $resultDelete);

        $values = $resultFetch2->getValues();

        $this->assertCount(2, $values);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $values[0]);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $values[0]);
        $this->assertEquals('[1,1,1]', (string)$values[0]->getValue());
        $this->assertEquals('[2,2,2]', (string)$values[1]->getValue());
    }

    public function testDomainObjectStoreAndFetchSingleValue()
    {
        $key      = uniqid();
        $object   = new SimpleObject('[1,1,1]');
        $location = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $store = StoreValue::builder($location, $object)
            ->withPw(1)
            ->withW(1)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withNotFoundOk(true)
            ->withR(1)
            ->build();

        $this->client->execute($store);

        $result = $this->client->execute($fetch);
        $domain = $result->getValue(SimpleObject::CLASS_NAME);

        $this->assertFalse($result->getNotFound());
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $result);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $domain);
        $this->assertEquals('[1,1,1]', $domain->getValue());

        $this->client->execute(DeleteValue::builder($location)
            ->build());
    }

    public function testDomainObjectStoreAndFetchValueWithSiblings()
    {
        $key      = uniqid();
        $object1  = new SimpleObject('[1,1,1]');
        $object2  = new SimpleObject('[2,2,2]');
        $location = new RiakLocation(new RiakNamespace('default', 'bucket'), $key);

        $store1 = StoreValue::builder($location, $object1)
            ->withPw(1)
            ->withW(1)
            ->build();

        $store2 = StoreValue::builder($location, $object2)
            ->withW(1)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withNotFoundOk(true)
            ->withR(1)
            ->build();

        $delete  = DeleteValue::builder($location)
            ->build();

        $resultFetch1  = $this->client->execute($fetch);
        $resultStore1  = $this->client->execute($store1);
        $resultStore2  = $this->client->execute($store2);
        $resultFetch2  = $this->client->execute($fetch);
        $resultDelete  = $this->client->execute($delete);
        $resultFetch3  = $this->client->execute($fetch);

        $this->assertTrue($resultFetch1->getNotFound());
        $this->assertFalse($resultFetch2->getNotFound());
        $this->assertTrue($resultFetch3->getNotFound());

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\StoreValueResponse', $resultStore1);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\StoreValueResponse', $resultStore2);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $resultFetch2);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\FetchValueResponse', $resultFetch3);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\DeleteValueResponse', $resultDelete);

        $values = $resultFetch2->getValues(SimpleObject::CLASS_NAME);

        $this->assertCount(2, $values);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $values[0]);
        $this->assertInstanceOf(SimpleObject::CLASS_NAME, $values[1]);

        $this->assertEquals($key, $values[0]->getRiakKey());
        $this->assertEquals($key, $values[1]->getRiakKey());
        $this->assertEquals('default', $values[0]->getRiakBucketType());
        $this->assertEquals('default', $values[1]->getRiakBucketType());
        $this->assertEquals('bucket', $values[0]->getRiakBucketName());
        $this->assertEquals('bucket', $values[1]->getRiakBucketName());
        $this->assertEquals('[1,1,1]', $values[0]->getValue());
        $this->assertEquals('[2,2,2]', $values[1]->getValue());
    }

    public function testGeneratedKey()
    {
        $object   = new RiakObject();
        $location = new RiakLocation(new RiakNamespace('default', 'bucket'), null);

        $object->setValue('[1,1,1]');
        $object->setContentType('application/json');

        $store = StoreValue::builder($location, $object)
            ->withReturnBody(true)
            ->withPw(1)
            ->withW(1)
            ->build();

        $result     = $this->client->execute($store);
        $riakObject = $result->getValue();

        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\StoreValueResponse', $result);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakObject', $riakObject);
        $this->assertEquals('[1,1,1]', $riakObject->getValue());
        $this->assertNotNull($result->getGeneratedKey());

        $location->setKey($result->getGeneratedKey());

        $this->client->execute(DeleteValue::builder($location)
            ->build());
    }

    public function testListKeys()
    {
        $key       = uniqid();
        $object    = new RiakObject();
        $namespace = new RiakNamespace('default', 'bucket');
        $location  = new RiakLocation($namespace, $key);

        $object->setValue('[1,1,1]');
        $object->setContentType('application/json');

        $this->client->execute(StoreValue::builder($location, $object)
            ->withPw(RiakOption::ALL)
            ->withW(RiakOption::ALL)
            ->withReturnBody(true)
            ->build());

        $command = ListKeys::builder($namespace)
            ->withNamespace($namespace)
            ->build();

        $result    = $this->client->execute($command);
        $iterator  = $result->getIterator();
        $locations = [];

        $this->assertInternalType('array', $locations);
        $this->assertInstanceOf('Iterator', $iterator);
        $this->assertInstanceOf('Riak\Client\Command\Kv\Response\ListKeysResponse', $result);

        foreach ($result->getLocations() as $location) {
            $locations[$location->getKey()] = $location;
        }

        $this->assertArrayHasKey($key, $locations);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $locations[$key]);
        $this->assertEquals($namespace, $locations[$key]->getNamespace());
        $this->assertEquals($key, $locations[$key]->getKey());
    }
}