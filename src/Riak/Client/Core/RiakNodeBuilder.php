<?php

namespace Riak\Client\Core;

use GuzzleHttp\Client;
use Riak\Client\RiakException;
use Riak\Client\Core\Transport\Proto\ProtoClient;
use Riak\Client\Core\Transport\Proto\ProtoConnection;

/**
 * Riak Node builder.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakNodeBuilder
{
    /**
     * @var string
     */
    private $protocol = 'http';

    /**
     * @var string
     */
    private $host = 'localhost';

    /**
     * @var string
     */
    private $port = 8098;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @param string $protocol
     *
     * @return \Riak\Client\Core\RiakNodeBuilder
     */
    public function withProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * @param string $host
     *
     * @return \Riak\Client\Core\RiakNodeBuilder
     */
    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @param string $port
     *
     * @return \Riak\Client\Core\RiakNodeBuilder
     */
    public function withPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @param string $user
     *
     * @return \Riak\Client\Core\RiakNodeBuilder
     */
    public function withUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param string $pass
     *
     * @return \Riak\Client\Core\RiakNodeBuilder
     */
    public function withPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * @return \Riak\Client\Core\RiakHttpTransport
     */
    private function buildHttpTransport()
    {
        $auth     = $this->user ? [$this->user, $this->pass] : null;
        $baseUrl  = "{$this->protocol}://{$this->host}:{$this->port}";
        $defaults = $auth ? [ 'auth'  => $auth ] : null;
        $client   = new Client([
            'base_url'  => $baseUrl,
            'defaults'  => $defaults,
        ]);

        return new RiakHttpTransport($client);
    }

    /**
     * @return \Riak\Client\Core\RiakProtoTransport
     */
    private function buildProtoTransport()
    {
        $rpbConn      = new ProtoConnection($this->host, $this->port);
        $rpbClient    = new ProtoClient($rpbConn);
        $riakPbAdpter = new RiakProtoTransport($rpbClient);

        return $riakPbAdpter;
    }

    /**
     * @return \Riak\Client\Core\RiakTransport
     */
    private function buildTransport()
    {
        if ($this->protocol == 'http' || $this->protocol == 'https') {
            return $this->buildHttpTransport();
        }

        if ($this->protocol == 'proto') {
            return $this->buildProtoTransport();
        }

        throw new RiakException("Unknown protocol : {$this->protocol}");
    }

    /**
     * @return \Riak\Client\Core\RiakNode
     */
    public function build()
    {
        return new RiakNode($this->buildTransport());
    }
}
