<?php

namespace Riak\Client\Core\Transport;

use Riak\Client\RiakException;
use GuzzleHttp\Exception\RequestException;

/**
 * Riak transport exception
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakTransportException extends RiakException
{
    /**
     * @param integer $code
     *
     * @return \Riak\Client\Core\Transport\RiakTransportException
     */
    public static function unexpectedStatusCode($code)
    {
        return new self(sprintf('Unexpected status code : "%s"', $code));
    }

    /**
     * @param \GuzzleHttp\Exception\RequestException $previous
     *
     * @return \Riak\Client\Core\Transport\RiakTransportException
     */
    public static function httpRequestException(RequestException $previous)
    {
        return new self($previous->getMessage(), $previous->getCode(), $previous);
    }
}
