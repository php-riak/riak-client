<?php

namespace RiakClientFixture\Data\Search;

use Riak\Client\Command\Bucket\StoreBucketProperties;
use Riak\Client\Command\DataType\StoreMap;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakObject;

class ArticlesData extends BaseSearchData
{
    protected function setUpBucket()
    {
        $store = StoreBucketProperties::builder()
            ->withSearchIndex($this->indexName)
            ->withNamespace($this->namespace)
            ->withAllowMulti(true)
            ->build();

        $this->client->execute($store);
    }

    public function storeDataTypes()
    {
        $this->storeArticleCrdt('crdt1', [
            'name'      => 'Riak for dummies',
            'category'  => ['it', 'comedy'],
            'author'    => [
                'name'  => 'Fabio B. Silva',
                'email' => 'fabio.bat.silva@gmail.com',
            ],
        ]);

        $this->storeArticleCrdt('crdt2', [
            'name'      => 'PHP for dummies',
            'category'  => ['it', 'comedy'],
            'author'    => [
                'name'  => 'Fabio B. Silva',
                'email' => 'fabio.bat.silva@gmail.com',
            ],
        ]);
    }

    public function storeRiakObjects()
    {
        $this->storeObject('kv1', new RiakObject(json_encode([
            'name_s'         => 'Riak for dummies',
            'category_ss'    => ['it', 'comedy'],
            'author_name_s'  => 'Fabio B. Silva',
            'author_email_s' => 'fabio.bat.silva@gmail.com',
        ]), 'application/json'));

        $this->storeObject('kv2', new RiakObject(json_encode([
            'name_s'         => 'PHP for dummies',
            'category_ss'    => ['it', 'comedy'],
            'author_name_s'  => 'Fabio B. Silva',
            'author_email_s' => 'fabio.bat.silva@gmail.com',
        ]), 'application/json'));
    }

    private function storeArticleCrdt($key, $data)
    {
        $location = new RiakLocation($this->namespace, $key);
        $store    = StoreMap::builder()
            ->withLocation($location)
            ->updateMap('author', $data['author'])
            ->updateRegister('name', $data['name'])
            ->updateSet('category', $data['category'])
            ->updateSet('set', $data['category'])
            ->build();

        $this->client->execute($store);
        $this->insureIsIndexed($key);
    }
}