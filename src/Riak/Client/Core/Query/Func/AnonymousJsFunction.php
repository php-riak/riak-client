<?php

namespace Riak\Client\Core\Query\Func;

/**
 * An Anonymous JS function.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class AnonymousJsFunction implements RiakFunction
{
    /**
     * @var string
     */
    private $source;

    /**
     * @param string $source
     */
    public function __construct($source)
    {
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'language' => 'javascript',
            'source'   => $this->source,
        ];
    }
}
