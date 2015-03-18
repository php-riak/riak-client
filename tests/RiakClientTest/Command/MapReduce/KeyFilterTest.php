<?php

namespace RiakClientTest\Command\MapReduce;

use RiakClientTest\TestCase;
use Riak\Client\Command\MapReduce\KeyFilters;

class KeyFilterTest extends TestCase
{
    public function testJsonEncode()
    {
        $this->assertEquals('[["starts_with","2005"]]', json_encode(KeyFilters::filter()->startsWith('2005')));
        $this->assertEquals('[["ends_with","-01"]]', json_encode(KeyFilters::filter()->endsWith('-01')));
    }

    public function testJsonEncodeAndComparison()
    {
        $filter = KeyFilters::filter()->and(
            KeyFilters::filter()->startsWith('2005'),
            KeyFilters::filter()->endsWith('-01')
        );

        $this->assertEquals('[["and",[["starts_with","2005"]],[["ends_with","-01"]]]]', json_encode($filter));
    }

    public function testJsonEncodeOrComparison()
    {
        $filter = KeyFilters::filter()->or(
            KeyFilters::filter()->matches('2005'),
            KeyFilters::filter()->matches('2006')
        );

        $this->assertEquals('[["or",[["matches","2005"]],[["matches","2006"]]]]', json_encode($filter));
    }

    public function testJsonEncodeSetMember()
    {
        $filter = KeyFilters::filter()
            ->setMember(["basho", "google", "yahoo"]);

        $this->assertEquals('[["set_member","basho","google","yahoo"]]', json_encode($filter));
    }

    public function testJsonEncodeNotMatch()
    {
        $filter = KeyFilters::filter()->not(
            KeyFilters::filter()->matches('solution')
        );

        $this->assertEquals('[["not",[["matches","solution"]]]]', json_encode($filter));
    }
}