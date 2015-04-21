<?php

namespace Riak\Client\Command\Kv\Response;

/**
 * Fetch Value Response.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class FetchValueResponse extends ObjectResponse
{
    /**
     * @var boolean
     */
    private $notFound;

    /**
     * @var boolean
     */
    private $unchanged;

    /**
     * @return boolean
     */
    public function getNotFound()
    {
        return $this->notFound;
    }

    /**
     * @return boolean
     */
    public function getUnchanged()
    {
        return $this->unchanged;
    }

    /**
     * @param boolean $notFound
     */
    public function setNotFound($notFound)
    {
        $this->notFound = $notFound;
    }

    /**
     * @param boolean $unchanged
     */
    public function setUnchanged($unchanged)
    {
        $this->unchanged = $unchanged;
    }
}
