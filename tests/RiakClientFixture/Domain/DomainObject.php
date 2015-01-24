<?php

namespace RiakClientFixture\Domain;

use Riak\Client\Annotation as Riak;

class DomainObject
{
    const CLASS_NAME = __CLASS__;

    /**
     * @var string
     *
     * @Riak\ContentType
     */
    private $contentType = 'plain/text';

    /**
     * @var string
     *
     * @Riak\VClock
     */
    private $vClock;

    /**
     * @var array
     */
    private $values = [];

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    function getContentType()
    {
        return $this->contentType;
    }

    function getVClock()
    {
        return $this->vClock;
    }

    function getValues()
    {
        return $this->values;
    }

    function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    function setVClock($vClock)
    {
        $this->vClock = $vClock;
    }

    function setValues($values)
    {
        $this->values = $values;
    }
}
