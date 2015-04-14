<?php

namespace RiakClientFunctionalTest\Command\DataType;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\FetchMap;
use Riak\Client\Command\DataType\StoreMap;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class MapTest extends TestCase
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

        $namespace = new RiakNamespace('maps', 'maps');
        $command   = StoreBucketProperties::builder()
            ->withAllowMulti(true)
            ->withNVal(3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($command);

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

    public function testStoreAndFetchSimpleMap()
    {
        $store = StoreMap::builder()
            ->withReturnBody(true)
            ->withPw(2)
            ->withDw(2)
            ->withW(3)
            ->withLocation($this->location)
            ->updateRegister('url', 'google.com')
            ->updateCounter('clicks', 100)
            ->updateFlag('active', true)
            ->build();

        $fetch = FetchMap::builder()
            ->withNotFoundOk(true)
            ->withPr(1)
            ->withR(1)
            ->withLocation($this->location)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\StoreMapResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchMapResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $fetchResponse->getDatatype());

        $this->assertEquals('google.com', $fetchResponse->getDatatype()->get('url'));
        $this->assertEquals(100, $fetchResponse->getDatatype()->get('clicks'));
        $this->assertTrue($fetchResponse->getDatatype()->get('active'));
        $this->assertEquals($this->location, $fetchResponse->getLocation());
    }

    public function testStoreAndFetchSetsWithinMaps()
    {
        $store = StoreMap::builder()
            ->withReturnBody(true)
            ->withPw(2)
            ->withDw(2)
            ->withW(3)
            ->withLocation($this->location)
            ->updateSet('interests', ['robots', 'opera', 'motorcycles'])
            ->build();

        $fetch = FetchMap::builder()
            ->withNotFoundOk(true)
            ->withPr(1)
            ->withR(1)
            ->withLocation($this->location)
            ->build();

        $this->client->execute($store);

        $fetchResponse = $this->client->execute($fetch);

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchMapResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $fetchResponse->getDatatype());

        $datatype  = $fetchResponse->getDatatype();
        $interests = $datatype->get('interests');

        $this->assertContains('opera', $interests);
        $this->assertContains('robots', $interests);
        $this->assertContains('motorcycles', $interests);
    }

    public function testStoreAndFetchMapsWithinMaps()
    {
        $store = StoreMap::builder()
            ->withReturnBody(true)
            ->withPw(2)
            ->withDw(2)
            ->withW(3)
            ->withLocation($this->location)
            ->updateRegister('username', 'FabioBatSilva')
            ->updateMap('info', [
                'first_name' => 'Fabio',
                'last_name'  => 'B. Silva',
                'interests'  => ['php', 'riak'],
                'email'      => 'fabio.bat.silva@gmail.com'
            ])
            ->build();

        $fetch = FetchMap::builder()
            ->withLocation($this->location)
            ->withNotFoundOk(true)
            ->withPr(1)
            ->withR(1)
            ->build();

        $this->client->execute($store);

        $fetchResponse = $this->client->execute($fetch);

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchMapResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $fetchResponse->getDatatype());

        $datatype  = $fetchResponse->getDatatype();
        $userName  = $datatype->get('username');
        $userInfo  = $datatype->get('info');

        $this->assertEquals('FabioBatSilva', $userName);
        $this->assertArrayHasKey('email', $userInfo);
        $this->assertArrayHasKey('last_name', $userInfo);
        $this->assertArrayHasKey('first_name', $userInfo);
        $this->assertArrayHasKey('interests', $userInfo);
        $this->assertContains('riak', $userInfo['interests']);
        $this->assertContains('php', $userInfo['interests']);
        $this->assertEquals('Fabio', $userInfo['first_name']);
        $this->assertEquals('B. Silva', $userInfo['last_name']);
        $this->assertEquals('fabio.bat.silva@gmail.com', $userInfo['email']);
    }

    public function testUpdateMapUsingContext()
    {
        $storeRespose1 = $this->client->execute(StoreMap::builder()
            ->withIncludeContext(true)
            ->withReturnBody(true)
            ->withLocation($this->location)
            ->updateRegister('username', 'FabioBatSilva')
            ->updateFlag('active', false)
            ->build());

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\StoreMapResponse', $storeRespose1);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $storeRespose1->getDataType());
        $this->assertInternalType('string', $storeRespose1->getContext());

        $store2Response = $this->client->execute(StoreMap::builder()
            ->withIncludeContext(true)
            ->withReturnBody(true)
            ->withContext($storeRespose1->getContext())
            ->withLocation($this->location)
            ->updateCounter('clicks', 1)
            ->updateFlag('active', true)
            ->build());


        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\StoreMapResponse', $store2Response);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $store2Response->getDataType());
        $this->assertInternalType('string', $store2Response->getContext());

        $fetchResponse = $this->client->execute(FetchMap::builder()
            ->withIncludeContext(true)
            ->withLocation($this->location)
            ->build());

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchMapResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $fetchResponse->getDataType());
        $this->assertInternalType('string', $fetchResponse->getContext());
    }
}