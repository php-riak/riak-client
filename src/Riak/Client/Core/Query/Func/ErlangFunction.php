<?php

namespace Riak\Client\Core\Query\Func;

/**
 * An Erlang Functions.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class ErlangFunction implements RiakFunction
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
}
