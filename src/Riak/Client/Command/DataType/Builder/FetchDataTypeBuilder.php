<?php

namespace Riak\Client\Command\DataType\Builder;

use Riak\Client\RiakOption;

/**
 * Used to construct a CRDT command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
abstract class FetchDataTypeBuilder extends Builder
{
    /**
     * Set the r value.
     *
     * @param integer $r
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchDataTypeBuilder
     */
    public function withR($r)
    {
        return $this->withOption(RiakOption::R, $r);
    }

    /**
     * Set the pr value.
     *
     * @param integer $pr
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchDataTypeBuilder
     */
    public function withPr($pr)
    {
        return $this->withOption(RiakOption::PR, $pr);
    }

    /**
     * Set the not_found_ok value.
     *
     * @param boolean $ok
     *
     * @return \Riak\Client\Command\DataType\Builder\FetchDataTypeBuilder
     */
    public function withNotFoundOk($ok)
    {
        return $this->withOption(RiakOption::NOTFOUND_OK, $ok);
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
