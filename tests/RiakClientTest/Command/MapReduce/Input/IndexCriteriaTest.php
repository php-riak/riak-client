<?php

namespace RiakClientTest\Command\MapReduce\Input;

use RiakClientTest\TestCase;
use Riak\Client\Command\MapReduce\Input\Index\RangeCriteria;
use Riak\Client\Command\MapReduce\Input\Index\MatchCriteria;

class IndexCriteriaTest extends TestCase
{
    public function testRangeCriteria()
    {
        $criteria = new RangeCriteria('start', 'end');

        $this->assertEquals('end', $criteria->getEnd());
        $this->assertEquals('start', $criteria->getStart());
        $this->assertEquals('{"start":"start","end":"end"}', json_encode($criteria));
    }

    public function testMatchCriteria()
    {
        $criteria = new MatchCriteria('value');

        $this->assertEquals('value', $criteria->getValue());
        $this->assertEquals('{"key":"value"}', json_encode($criteria));
    }
}