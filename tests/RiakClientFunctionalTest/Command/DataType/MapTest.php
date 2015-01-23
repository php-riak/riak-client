<?php

namespace RiakClientFunctionalTest\Command\DataType;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Cap\RiakOption;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\DataType\FetchMap;
use Riak\Client\Command\DataType\StoreMap;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class MapTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $namespace = new RiakNamespace('maps', 'maps');
        $store     = StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($namespace)
            ->build();

        $this->client->execute($store);
    }

    public function testStoreAndFetchSimpleMap()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('maps', 'maps'), $key);

        $store = StoreMap::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->updateRegister('url', 'google.com')
            ->updateCounter('clicks', 100)
            ->updateFlag('active', true)
            ->build();

        $fetch = FetchMap::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);

        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\StoreMapResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\DataType\Response\FetchMapResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Crdt\RiakMap', $fetchResponse->getDatatype());

        $this->assertEquals('google.com', $fetchResponse->getDatatype()->get('url'));
        $this->assertEquals(100, $fetchResponse->getDatatype()->get('clicks'));
        $this->assertTrue($fetchResponse->getDatatype()->get('active'));
        $this->assertEquals($location, $fetchResponse->getLocation());
    }

    public function testStoreAndFetchSetsWithinMaps()
    {
        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('maps', 'maps'), $key);

        $store = StoreMap::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->updateSet('interests', ['robots', 'opera', 'motorcycles'])
            ->build();

        $fetch = FetchMap::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
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
        if ($this instanceof MapProtoTest) {
            $this->markTestIncomplete();
        }

        $key      = uniqid();
        $location = new RiakLocation(new RiakNamespace('maps', 'maps'), $key);

        $store = StoreMap::builder()
            ->withOption(RiakOption::RETURN_BODY, true)
            ->withOption(RiakOption::PW, 2)
            ->withOption(RiakOption::DW, 1)
            ->withOption(RiakOption::W, 3)
            ->withLocation($location)
            ->updateRegister('username', 'FabioBatSilva')
            ->updateMap('info', [
                'first_name' => 'Fabio',
                'last_name'  => 'B. Silva',
                'email'      => 'fabio.bat.silva@gmail.com'
            ])
            ->build();

        $fetch = FetchMap::builder()
            ->withOption(RiakOption::BASIC_QUORUM, true)
            ->withOption(RiakOption::NOTFOUND_OK, true)
            ->withOption(RiakOption::PR, 1)
            ->withOption(RiakOption::R, 1)
            ->withLocation($location)
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
        $this->assertArrayHasKey('first_name', $userInfo);
        $this->assertArrayHasKey('last_name', $userInfo);
        $this->assertEquals('Fabio', $userInfo['first_name']);
        $this->assertEquals('B. Silva', $userInfo['last_name']);
        $this->assertEquals('fabio.bat.silva@gmail.com', $userInfo['email']);
    }
}