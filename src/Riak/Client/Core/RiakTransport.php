<?php

namespace Riak\Client\Core;

use Riak\Client\Core\Message\Request;

/**
 * Riak transport.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
interface RiakTransport
{
    /**
     * @param \Riak\Client\Core\Message\Request $request
     *
     * @return \Riak\Client\Core\Message\Response
     *
     * @throws \Riak\Client\Core\Transport\RiakTransportException
     */
    public function send(Request $request);
}
