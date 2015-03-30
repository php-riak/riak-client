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
use Riak\Client\Command\Bucket\StoreBucketProperties;
use RiakClientFixture\Domain\SimpleObject;

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
            ->withOption(RiakOption::W, RiakOption::QUORUM)
            ->withOption(RiakOption::PW, RiakOption::ONE)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, RiakOption::QUORUM)
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
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $store2 = StoreValue::builder($location, $object2)
            ->withOption(RiakOption::W, 1)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
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
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
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
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $store2 = StoreValue::builder($location, $object2)
            ->withOption(RiakOption::W, 1)
            ->build();

        $fetch  = FetchValue::builder($location)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::R, 1)
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
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
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
}