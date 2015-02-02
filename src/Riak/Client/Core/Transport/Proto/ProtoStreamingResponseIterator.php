<?php

namespace Riak\Client\Core\Transport\Proto;

use Iterator;

/**
 * RPB response iterator
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class ProtoStreamingResponseIterator implements Iterator
{
    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoClient
     */
    protected $client;

    /**
     * @var integer
     */
    protected $count = 0;

    /**
     * @var \DrSlump\Protobuf\Message
     */
    protected $current;

    /**
     * @var integer
     */
    protected $messageCode;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoClient $client
     * @param integer                                       $messageCode
     */
    public function __construct(ProtoClient $client, $messageCode)
    {
        $this->client      = $client;
        $this->messageCode = $messageCode;
    }

    /**
     * @return \DrSlump\Protobuf\Message
     */
    protected function readNext()
    {
        return $this->client->receiveMessage($this->messageCode);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->count;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->count   = $this->count + 1;
        $this->current = $this->readNext();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if ($this->current != null) {
            throw new \RuntimeException('A streaming iterator cannot be rewind');
        }

        $this->count   = 0;
        $this->current = $this->readNext();
    }
}
