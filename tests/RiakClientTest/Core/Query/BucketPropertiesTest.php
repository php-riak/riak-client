<?php

namespace RiakClientTest\Core\Query;

use RiakClientTest\TestCase;
use Riak\Client\Core\Query\BucketProperties;

class BucketPropertiesTest extends TestCase
{
    public function testGetValuesFromMap()
    {
        $props = new BucketProperties([
            BucketProperties::R                => 1,
            BucketProperties::W                => 2,
            BucketProperties::PR               => 3,
            BucketProperties::PW               => 4,
            BucketProperties::DW               => 5,
            BucketProperties::RW               => 6,
            BucketProperties::N_VAL            => 7,
            BucketProperties::ALLOW_MULT       => true,
            BucketProperties::LAST_WRITE_WINS  => false,
            BucketProperties::PRECOMMIT_HOOKS  => [],
            BucketProperties::POSTCOMMIT_HOOKS => [],
            BucketProperties::OLD_VCLOCK       => 8,
            BucketProperties::YOUNG_VCLOCK     => 9,
            BucketProperties::BIG_VCLOCK       => 10,
            BucketProperties::SMALL_VCLOCK     => 11,
            BucketProperties::BASIC_QUORUM     => true,
            BucketProperties::NOTFOUND_OK      => false,
            BucketProperties::SEARCH           => true,
            BucketProperties::CONSISTENT       => false,
            BucketProperties::BACKEND          => 'backend',
            BucketProperties::DATATYPE         => 'data-type',
            BucketProperties::SEARCH_INDEX     => 'search-index',
            BucketProperties::NAME             => 'bucket-name',
        ]);

        $this->assertEquals(1, $props->getR());
        $this->assertEquals(2, $props->getW());
        $this->assertEquals(3, $props->getPr());
        $this->assertEquals(4, $props->getPw());
        $this->assertEquals(5, $props->getDw());
        $this->assertEquals(6, $props->getRw());
        $this->assertEquals(7, $props->getNVal());
        $this->assertEquals(true, $props->getAllowMult());
        $this->assertEquals(false, $props->getLastWriteWins());
        $this->assertEquals([], $props->getPostCommitHooks());
        $this->assertEquals([], $props->getPreCommitHooks());
        $this->assertEquals(8, $props->getOldVClock());
        $this->assertEquals(9, $props->getYoungVClock());
        $this->assertEquals(10, $props->getBigVClock());
        $this->assertEquals(11, $props->getSmallVClock());
        $this->assertEquals(true, $props->getBasicQuorum());
        $this->assertEquals(false, $props->getNotFoundOk());
        $this->assertEquals(true, $props->getSearch());
        $this->assertEquals(false, $props->getConsistent());
        $this->assertEquals('backend', $props->getBackend());
        $this->assertEquals('data-type', $props->getDatatype());
        $this->assertEquals('bucket-name', $props->getName());
        $this->assertEquals('search-index', $props->getSearchIndex());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Unknown property 'UNKNOWN_PROPERTY' on 'Riak\Client\Core\Query\BucketProperties'
     */
    public function testUnknownPropertyException()
    {
        new BucketProperties(['UNKNOWN_PROPERTY' => 'UNKNOWN_VALUE']);
    }
}