<?php

namespace Riak\Client\Core\Transport\Proto;

use Riak\Client\Core\RiakIterator;
use DrSlump\Protobuf\Message;

/**
 * Iterates ofer a streaming iterator while is not "done"
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class ProtoStreamIteratorIterator extends RiakIterator
{
    /**
     * @var \DrSlump\Protobuf\Message
     */
    protected $message;

    /**
     * @var \Riak\Client\Core\Transport\Proto\ProtoStreamIterator
     */
    protected $iterator;

    /**
     * @param \Riak\Client\Core\Transport\Proto\ProtoStreamIterator $iterator
     */
    public function __construct(ProtoStreamIterator $iterator)
    {
        $this->iterator = $iterator;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->iterator->next();

        parent::next();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->iterator->rewind();

        parent::rewind();
    }

    /**
     * @return \Iterator
     */
    protected function readNext()
    {
        if ($this->isDone()) {
            return null;
        }

        $this->message = $this->iterator->current();

        return $this->extract($this->message);
    }

    /**
     * @return boolean
     */
    protected function isDone()
    {
        if ($this->message === null) {
            return false;
        }

        if ($this->message->hasDone() && $this->message->done) {
            return true;
        }

        return false;
    }

    /**
     * @param \DrSlump\Protobuf\Message $message
     *
     * @return mixed
     */
    abstract protected function extract(Message $message);
}
