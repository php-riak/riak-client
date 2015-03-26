<?php

namespace RiakClientTest\Command\MapReduce\Input;

use RiakClientTest\TestCase;
use Riak\Client\Command\MapReduce\Input\SearchInput;

class SearchInputTest extends TestCase
{
    public function testSearchInput()
    {
        $input = new SearchInput('index-name', 'name:fabio');

        $this->assertEquals('index-name', $input->getIndex());
        $this->assertEquals('name:fabio', $input->getQuery());
        $this->assertEquals('{"module":"yokozuna","function":"mapred_search","arg":["index-name","name:fabio"]}', json_encode($input));
    }
}