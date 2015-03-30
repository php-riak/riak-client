<?php

namespace Riak\Client\Core\Query\Func;

/**
 * An Erlang Functions.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ErlangFunction implements RiakFunction, RiakPropertyFunction
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $function;

    /**
     * @param string $module
     * @param string $function
     */
    public function __construct($module, $function)
    {
        $this->module   = $module;
        $this->function = $function;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return [
            'language' => 'erlang',
            'module'   => $this->module,
            'function' => $this->function
        ];
    }

    /**
     * @param array $func
     *
     * @return \Riak\Client\Core\Query\Func\ErlangFunction
     */
    public static function createFromArray(array $func)
    {
        if ( ! isset($func['module']) ||  ! isset($func['function'])) {
            throw new \InvalidArgumentException("Invalid function : " . json_encode($func));
        }

        return new ErlangFunction($func['module'], $func['function']);
    }
}
