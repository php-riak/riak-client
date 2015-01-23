<?php

namespace Riak\Client\Converter\Hydrator;

/**
 * Domain Metadata.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ClassMetadata
{
    /**
     * READ-ONLY: Whether this class describes the property mapping of a riak domain object.
     *
     * @var array
     */
    public $riakProperties = [];

    /**
     *  READ-ONLY: The name of the domain class.
     *
     * @var string
     */
    public $className = [];

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasRiakField($name)
    {
        return isset($this->riakProperties[$name]);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getRiakField($name)
    {
        return isset($this->riakProperties[$name])
            ? $this->riakProperties[$name]
            : null;
    }

    /**
     * @return string
     */
    public function getRiakKeyField()
    {
        return $this->getRiakField('key');
    }

    /**
     * @return string
     */
    public function getRiakBucketNameField()
    {
        return $this->getRiakField('bucketName');
    }

    /**
     * @return string
     */
    public function getRiakBucketTypeField()
    {
        return $this->getRiakField('bucketType');
    }

    /**
     * @return string
     */
    public function getRiakVClockField()
    {
        return $this->getRiakField('vClock');
    }

    /**
     * @return string
     */
    public function getRiakLastModifiedField()
    {
        return $this->getRiakField('lastModified');
    }

    /**
     * @return string
     */
    public function getRiakContentTypeField()
    {
        return $this->getRiakField('contentType');
    }
}
