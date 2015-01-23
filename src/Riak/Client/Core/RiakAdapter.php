<?php

namespace Riak\Client\Core;

use Riak\Client\Core\Message\Request;

/**
 * Riak Client Adpter.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface RiakAdapter
{
    /**
     * @param \Riak\Client\Core\Message\Request $request
     *
     * @return \Riak\Client\Core\Message\Response
     */
    public function send(Request $request);
}
