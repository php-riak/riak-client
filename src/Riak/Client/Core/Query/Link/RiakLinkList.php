<?php

namespace Riak\Client\Core\Query\Link;

use Riak\Client\Core\Query\RiakList;

/**
 * Represents list of riak links.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakLinkList extends RiakList
{
    /**
     * @param \Riak\Client\Core\Query\Link\RiakLink $link
     */
    public function addLink(RiakLink $link)
    {
        $this->list[] = $link;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function (RiakLink $l) {
            return $l->toArray();
        }, $this->list);
    }
}
