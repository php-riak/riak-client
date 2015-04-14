<?php

namespace RiakClientFixture\Data\Search;

use Riak\Client\Core\Query\RiakObject;

class ThunderCatsData extends BaseSearchData
{
    public function storeThunderCats()
    {
        $this->storeObject("lion", new RiakObject(json_encode([
            'name_s'   => 'Lion-o',
            'leader_b' => true,
            'age_i'    => 30,
        ]), 'application/json'));

        $this->storeObject("cheetara", new RiakObject(json_encode([
            'name_s'   => 'Cheetara',
            'leader_b' => false,
            'age_i'    => 30,
        ]), 'application/json'));

        $this->storeObject("snarf", new RiakObject(json_encode([
            'name_s'   => 'Snarf',
            'leader_b' => false,
            'age_i'    => 43,
        ]), 'application/json'));

        $this->storeObject("panthro", new RiakObject(json_encode([
            'name_s'   => 'Panthro',
            'leader_b' => false,
            'age_i'    => 36,
        ]), 'application/json'));
    }
}