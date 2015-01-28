<?php

namespace RiakClientFunctionalTest\Command\Search;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Command\Search\StoreSchema;
use Riak\Client\Command\Search\FetchSchema;
use Riak\Client\Core\Query\Search\YokozunaSchema;

abstract class SchemaTest extends TestCase
{
    private function getSchemaContent($name)
    {
        $filepath = '/../../../RiakClientFixture/search-schema';
        $content  = file_get_contents(__DIR__ . $filepath . '/' . $name);

        return $content;
    }

    public function testStoreAndFetchSchema()
    {
        $content = $this->getSchemaContent('schedule.xml');
        $schema  = new YokozunaSchema('schedule', $content);

        $store = StoreSchema::builder()
            ->withSchema($schema)
            ->build();

        $fetch = FetchSchema::builder()
            ->withSchemaName('schedule')
            ->build();

        $storeResponse = $this->client->execute($store);
        $fetchResponse = $this->client->execute($fetch);

        $this->assertInstanceOf('Riak\Client\Command\Search\Response\StoreSchemaResponse', $storeResponse);
        $this->assertInstanceOf('Riak\Client\Command\Search\Response\FetchSchemaResponse', $fetchResponse);
        $this->assertInstanceOf('Riak\Client\Core\Query\Search\YokozunaSchema', $fetchResponse->getSchema());
        $this->assertXmlStringEqualsXmlString($content, $fetchResponse->getSchema()->getContent());
        $this->assertEquals('schedule', $fetchResponse->getSchema()->getName());
    }
}