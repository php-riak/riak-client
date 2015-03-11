<?php

namespace Riak\Client\Core\Query;

/**
 * Encapsulates a Riak bucket type and bucket name.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class RiakNamespace
{
    /**
     * The default bucket type in Riak.
     */
    const DEFAULT_TYPE = "default";

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @param type $type
     * @param type $bucket
     */
    public function __construct($type, $bucket)
    {
        $this->bucket = $bucket;
        $this->type   = $type;
    }

    /**
     * Returns the bucket type for this Namespace.
     *
     * @return string
     */
    public function getBucketType()
    {
        return $this->type;
    }

    /**
     * Returns the bucket name for this Namespace.
     *
     * @return string
     */
    public function getBucketName()
    {
        return $this->bucket;
    }

    /**
     * @return boolean
     */
    public function isDefaultType()
    {
        return ($this->type == null) || ($this->type == self::DEFAULT_TYPE);
    }
}
