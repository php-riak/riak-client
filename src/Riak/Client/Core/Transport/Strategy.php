<?php

namespace Riak\Client\Core\Transport;

use Riak\Client\Core\Message\Request;

/**
 * Riak adapter strategy.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface Strategy
{
    /**
     * @param \Riak\Client\Core\Message\Request $request
     *
     * @return \Riak\Client\Core\Message\Response
     */
    public function send(Request $request);
}
