<?php

namespace RiakClientFunctionalTest\Command\Index;

use RiakClientFunctionalTest\TestCase;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Command\Index\IntIndexQuery;
use Riak\Client\Command\Index\BinIndexQuery;
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
            'value'  => 'User1',
            'groups' => [0, 1, 2],
            'emails' => [
                'user1@gmail.com',
                'user1@yahoo.com',
                'user1@hotmail.com'
            ],
        ],
        [
            'value'  => 'User2',
            'groups' => [0, 2, 3],
            'emails' => [
                'user2@gmail.com',
                'user2@yahoo.com',
                'user2@hotmail.com'
            ],
        ],
        [
            'value'  => 'User3',
            'groups' => [0, 3, 4],
            'emails' => [
                'user3@gmail.com',
                'user3@yahoo.com',
                'user3@hotmail.com'
            ],
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
            $this->client->execute(new DeleteValue($location));
        }

        parent::tearDown();
    }

    private function setUpBucket()
    {
        $this->client->execute(StoreBucketProperties::builder()
            ->withNamespace($this->namespace)
            ->withAllowMulti(true)
            ->withNVal(3)
            ->build());
    }

    private function setUpData()
    {
        $u1 = $this->data[0];
        $u2 = $this->data[1];
        $u3 = $this->data[2];

        $this->storeObject("user1", $u1['value'], $u1['groups'], $u1['emails']);
        $this->storeObject("user2", $u2['value'], $u2['groups'], $u2['emails']);
        $this->storeObject("user3", $u3['value'], $u3['groups'], $u3['emails']);
    }

    private function storeObject($key, $info, array $groups, array $emails)
    {
        $json     = json_encode($info);
        $object   = new RiakObject($json, 'application/json');
        $location = new RiakLocation($this->namespace, $key);
        $command  = StoreValue::builder($location, $object)
            ->withPw(1)
            ->withW(2)
            ->build();

        $object->addIndex(new RiakIndexInt('groups', $groups));
        $object->addIndex(new RiakIndexBin('emails', $emails));

        $this->client->execute($command);

        $this->locations[] = $location;
    }

    public function testSimpleIntIndexQueryMatch()
    {
        $indexQuery = IntIndexQuery::builder()
            ->withNamespace($this->namespace)
            ->withIndexName('groups')
            ->withReturnTerms(true)
            ->withMatch(2)
            ->build();

        $result = $this->client->execute($indexQuery);

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result);

        $values = $result->getEntries();

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

    public function testSimpleIntIndexQueryMatchWhitoutTerms()
    {
        $indexQuery = IntIndexQuery::builder()
            ->withNamespace($this->namespace)
            ->withReturnTerms(false)
            ->withIndexName('groups')
            ->withMatch(2)
            ->build();

        $result = $this->client->execute($indexQuery);

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result);

        $values = $result->getEntries();

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
        $indexQuery = IntIndexQuery::builder()
            ->withNamespace($this->namespace)
            ->withIndexName('groups')
            ->withReturnTerms(true)
            ->withStart(2)
            ->withEnd(3)
            ->build();

        $result = $this->client->execute($indexQuery);

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result);

        $iterator = $result->getIterator();
        $values   = iterator_to_array($iterator);

        $this->assertCount(4, $values);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[0]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[1]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[2]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[3]);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[0]->getLocation());
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[1]->getLocation());
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[2]->getLocation());
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[3]->getLocation());

        usort($values, function(IndexEntry $a, IndexEntry $b){
            return strcmp($a->getLocation()->getKey(), $b->getLocation()->getKey());
        });

        $this->assertEquals('user1', $values[0]->getLocation()->getKey());
        $this->assertEquals('user2', $values[1]->getLocation()->getKey());
        $this->assertEquals('user2', $values[2]->getLocation()->getKey());
        $this->assertEquals('user3', $values[3]->getLocation()->getKey());

        usort($values, function(IndexEntry $a, IndexEntry $b){
            return strcmp($a->getIndexKey(), $b->getIndexKey());
        });

        $this->assertEquals(2, $values[0]->getIndexKey());
        $this->assertEquals(2, $values[1]->getIndexKey());
        $this->assertEquals(3, $values[2]->getIndexKey());
        $this->assertEquals(3, $values[3]->getIndexKey());
    }

    public function testSimpleBinIndexQueryRange()
    {
        $indexQuery = BinIndexQuery::builder()
            ->withNamespace($this->namespace)
            ->withTermFilter('@gmail.com')
            ->withIndexName('emails')
            ->withReturnTerms(true)
            ->withStart('user1')
            ->withEnd('user4')
            ->build();

        $result = $this->client->execute($indexQuery);

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result);

        $values = $result->getEntries();

        $this->assertCount(3, $values);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[0]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[1]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values[2]);
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[0]->getLocation());
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[1]->getLocation());
        $this->assertInstanceOf('Riak\Client\Core\Query\RiakLocation', $values[2]->getLocation());

        usort($values, function(IndexEntry $a, IndexEntry $b){
            return strcmp($a->getLocation()->getKey(), $b->getLocation()->getKey());
        });

        $this->assertEquals('user1', $values[0]->getLocation()->getKey());
        $this->assertEquals('user2', $values[1]->getLocation()->getKey());
        $this->assertEquals('user3', $values[2]->getLocation()->getKey());
        $this->assertEquals('user1@gmail.com', $values[0]->getIndexKey());
        $this->assertEquals('user2@gmail.com', $values[1]->getIndexKey());
        $this->assertEquals('user3@gmail.com', $values[2]->getIndexKey());
    }

    public function testContinuationIndexQuery()
    {
        $builder = BinIndexQuery::builder()
            ->withNamespace($this->namespace)
            ->withTermFilter('@gmail.com')
            ->withIndexName('emails')
            ->withReturnTerms(true)
            ->withStart('user1')
            ->withMaxResults(2)
            ->withEnd('user4');

        $result1 = $this->client->execute($builder->build());

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result1);
        $this->assertFalse($result1->hasContinuation());

        $values1       = $result1->getEntries();
        $continuation1 = $result1->getContinuation();

        $this->assertCount(2, $values1);
        $this->assertNotNull($continuation1);
        $this->assertTrue($result1->hasContinuation());
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values1[0]);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values1[1]);

        usort($values1, function(IndexEntry $a, IndexEntry $b){
            return strcmp($a->getLocation()->getKey(), $b->getLocation()->getKey());
        });

        $this->assertEquals('user1', $values1[0]->getLocation()->getKey());
        $this->assertEquals('user2', $values1[1]->getLocation()->getKey());
        $this->assertEquals('user1@gmail.com', $values1[0]->getIndexKey());
        $this->assertEquals('user2@gmail.com', $values1[1]->getIndexKey());

        $result2 = $this->client->execute($builder
            ->withContinuation($continuation1)
            ->build());

        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexQueryResponse', $result2);
        $this->assertFalse($result2->hasContinuation());

        $iterator2     = $result2->getIterator();
        $values2       = iterator_to_array($iterator2);
        $continuation2 = $result2->getContinuation();

        $this->assertCount(1, $values2);
        $this->assertNull($continuation2);
        $this->assertInstanceOf('Riak\Client\Command\Index\Response\IndexEntry', $values2[0]);

        usort($values1, function(IndexEntry $a, IndexEntry $b){
            return strcmp($a->getLocation()->getKey(), $b->getLocation()->getKey());
        });

        $this->assertEquals('user3', $values2[0]->getLocation()->getKey());
        $this->assertEquals('user3@gmail.com', $values2[0]->getIndexKey());
    }
}