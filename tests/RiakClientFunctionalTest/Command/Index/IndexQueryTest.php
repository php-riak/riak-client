<?php

namespace RiakClientFunctionalTest\Command\Index;

use Riak\Client\RiakOption;
use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Index\IntIndexQuery;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Core\Query\Index\RiakIndexBin;
use Riak\Client\Core\Query\Index\RiakIndexInt;
use Riak\Client\Command\Index\Response\IndexEntry;
use Riak\Client\Command\Bucket\StoreBucketProperties;

abstract class IndexQueryTest extends TestCase
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    protected $namespace;

    /**
     * @var \Riak\Client\Core\Query\RiakLocation[]
     */
    protected $locations;

    /**
     * @var array
     */
    protected $data = [
        [
            'value' => 'User1',
            'keys'  => [0, 1, 2],
            'tags'  => ['t0', 't1', 't2'],
        ],
        [
            'value' => 'User2',
            'keys'  => [0, 2, 3],
            'tags'  => ['t0', 't2', 't3'],
        ],
        [
            'value' => 'User3',
            'keys'  => [0, 3, 4],
            'tags'  => ['t0', 't3', 't4'],
        ]
    ];

    protected function setUp()
    {
        parent::setUp();

        $hash      = hash('crc32', __CLASS__ );
        $bucket    = sprintf('test_riak_client_%s_index_query', $hash);
        $namespace = new RiakNamespace('default', $bucket);

        $this->namespace = $namespace;

        $this->setUpBucket();
        $this->setUpData();
    }

    protected function tearDown()
    {
        foreach ($this->locations as $location) {
            $this->client->execute(new DeleteValue($location, []));
        }

        parent::tearDown();
    }

    private function setUpBucket()
    {
        $this->client->execute(StoreBucketProperties::builder()
            ->withProperty(BucketProperties::ALLOW_MULT, true)
            ->withProperty(BucketProperties::N_VAL, 3)
            ->withNamespace($this->namespace)
            ->build());
    }

    private function setUpData()
    {
        $u1 = $this->data[0];
        $u2 = $this->data[1];
        $u3 = $this->data[2];

        $this->storeObject("user1", $u1['value'], $u1['keys'], $u1['tags']);
        $this->storeObject("user2", $u2['value'], $u2['keys'], $u2['tags']);
        $this->storeObject("user3", $u3['value'], $u3['keys'], $u3['tags']);
    }

    private function storeObject($key, $info, array $keys, array $tags)
    {
        $json     = json_encode($info);
        $object   = new RiakObject($json, 'application/json');
        $location = new RiakLocation($this->namespace, $key);
        $command  = StoreValue::builder($location, $object)
            ->withOption(RiakOption::PW, 1)
            ->withOption(RiakOption::W, 2)
            ->build();

        $object->addIndex(new RiakIndexInt('keys', $keys));
        $object->addIndex(new RiakIndexBin('tags', $tags));

        $this->client->execute($command);

        $this->locations[] = $location;
    }

    public function testSimpleIntIndexQueryMatch()
    {
        $this->setUpData();

        $indexQuery = IntIndexQuery::builder()
            ->withNamespace($this->namespace)
            ->withIndexName('keys')
            ->withReturnTerms(true)
            ->withMatch(2)
            ->build();

        $result = $this->client->execute($indexQuery);

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result);

        $iterator = $result->getEntries();
        $values   = iterator_to_array($iterator);

        $this->assertCount(2, $values);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[0]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[1]);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[0]->getLocation());
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[1]->getLocation());

        usort($values, function(IndexEntry $a, IndexEntry $b){
            return strcmp($a->getLocation()->getKey(), $b->getLocation()->getKey());
        });

        $this->assertEquals('user1', $values[0]->getLocation()->getKey());
        $this->assertEquals('user2', $values[1]->getLocation()->getKey());
        $this->assertEquals(2, $values[0]->getIndexKey());
        $this->assertEquals(2, $values[1]->getIndexKey());
    }

    public function testSimpleIntIndexQueryRange()
    {
        $this->setUpData();

        $indexQuery = IntIndexQuery::builder()
            ->withNamespace($this->namespace)
            ->withIndexName('keys')
            ->withReturnTerms(true)
            ->withStart(2)
            ->withEnd(3)
            ->build();

        $result = $this->client->execute($indexQuery);

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result);

        $iterator = $result->getEntries();
        $values   = iterator_to_array($iterator);

        $this->assertCount(4, $values);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[0]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[1]);
    }
}