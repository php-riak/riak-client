<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\RiakOption;

/**
 * Used to construct a CRDT command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class StoreDataTypeBuilder extends Builder
{
    /**
     * Set the pw value.
     *
     * @param integer $pw
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withPw($pw)
    {
        return $this->withOption(RiakOption::PW, $pw);
    }

    /**
     * Set the dw value.
     *
     * @param integer $dw
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withDw($dw)
    {
        return $this->withOption(RiakOption::DW, $dw);
    }

    /**
     * Set the w value.
     *
     * @param integer $w
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withW($w)
    {
        return $this->withOption(RiakOption::W, $w);
    }

    /**
     * Set the returnbody value.
     *
     * @param integer $flag
     *
     * @return \Riak\Client\Command\Kv\Builder\StoreValueBuilder
     */
    public function withReturnBody($flag)
    {
        return $this->withOption(RiakOption::RETURN_BODY, $flag);
    }

    /**
     * Include the context from a previous fetch.
     *
     * @param string $context
     *
     * @return \Riak\Client\Command\DataType\Builder
     */
    public function withContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Set the include_context value.
     *
     * @param boolean $include
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchDataTypeBuilder
     */
    public function withIncludeContext($include)
    {
        return $this->withOption(RiakOption::INCLUDE_CONTEXT, $include);
    }
}
