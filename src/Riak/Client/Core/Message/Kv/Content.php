<?php

namespace Riak\Client\Core\Message\Kv;

/**
 * This class represents a message content.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class Content
{
    public $value;
    public $vtag;
    public $contentType;
    public $lastModified;
    public $links = [];
    public $metas = [];
    public $indexes = [];
    public $deleted = [];
}
