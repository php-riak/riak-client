<?php

namespace Riak\Client\Core\Query\Func;

/**
 * A Named JS function.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class NamedJsFunction implements RiakFunction, RiakPropertyFunction
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'language' => 'javascript',
            'name'     => $this->name
        ];
    }

    /**
     * @param array $func
     *
     * @return \Riak\Client\Core\Query\Func\NamedJsFunction
     */
    public static function createFromArray(array $func)
    {
        if ( ! isset($func['name'])) {
            throw new \InvalidArgumentException("Invalid function : " . json_encode($func));
        }

        return new NamedJsFunction($func['name']);
    }
}
