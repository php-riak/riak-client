<?php

namespace Riak\Client\Command\Bucket\Builder;

use Riak\Client\Command\Bucket\StoreBucketProperties;
use Riak\Client\Core\Query\Func\RiakPropertyFunction;
use Riak\Client\Core\Query\BucketProperties;
use Riak\Client\Core\Query\RiakNamespace;

/**
 * Used to construct a StoreBucketProperties command.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class StoreBucketPropertiesBuilder extends Builder
{
    /**
     * @var \Riak\Client\Core\Query\RiakNamespace
     */
    private $namespace;

    /**
     * @var array
     */
    private $properties;

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     */
    public function __construct(RiakNamespace $namespace = null)
    {
        $this->namespace  = $namespace;
        $this->properties = [];
    }

    /**
     * @param \Riak\Client\Core\Query\RiakNamespace $namespace
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withNamespace(RiakNamespace $namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Set the allow_multi value.
     *
     * @param boolean $allow
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withAllowMulti($allow)
    {
        return $this->withProperty(BucketProperties::ALLOW_MULT, $allow);
    }

    /**
     * Set the backend used by this bucket.
     *
     * @param string $backend
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withBackend($backend)
    {
        return $this->withProperty(BucketProperties::BACKEND, $backend);
    }

    /**
     * Set the basic_quorum value.
     *
     * @param boolean $use
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withBasicQuorum($use)
    {
        return $this->withProperty(BucketProperties::BASIC_QUORUM, $use);
    }

    /**
     * Set the big_vclock representing a epoch time value.
     *
     * @param integer $bigVClock
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilderVector Clock Pruning</a>
     */
    public function withBigVClock($bigVClock)
    {
        return $this->withProperty(BucketProperties::BIG_VCLOCK, $bigVClock);
    }

    /**
     * Set the last_write_wins value, wins whether to ignore vector clocks when writing.
     *
     * @param boolean $wins
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withLastWriteWins($wins)
    {
        return $this->withProperty(BucketProperties::LAST_WRITE_WINS, $wins);
    }

    /**
     * Set the linkfun value.
     *
     * @param \Riak\Client\Core\Query\Func\RiakPropertyFunction $function
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withLinkwalkFunction(RiakPropertyFunction $function)
    {
        return $this->withProperty(BucketProperties::LINKWALK_FUNCTION, $function->jsonSerialize());
    }

    /**
     * Set the chash_keyfun value.
     *
     * @param \Riak\Client\Core\Query\Func\RiakPropertyFunction $function
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withChashkeyFunction(RiakPropertyFunction $function)
    {
        return $this->withProperty(BucketProperties::CHASH_KEY_FUNCTION, $function->jsonSerialize());
    }

    /**
     * Set the rw value.
     *
     * @param integer $rw
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withRw($rw)
    {
        return $this->withProperty(BucketProperties::RW, $rw);
    }

    /**
     * Set the dw value.
     *
     * @param integer $dw
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withDw($dw)
    {
        return $this->withProperty(BucketProperties::DW, $dw);
    }

    /**
     * Set the w value.
     *
     * @param integer $w
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withW($w)
    {
        return $this->withProperty(BucketProperties::W, $w);
    }

    /**
     * Set the r value.
     *
     * @param integer $r
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withR($r)
    {
        return $this->withProperty(BucketProperties::R, $r);
    }

    /**
     * Set the pr value.
     *
     * @param integer $pr
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withPr($pr)
    {
        return $this->withProperty(BucketProperties::PR, $pr);
    }

    /**
     * Set the pw value.
     *
     * @param integer $pw
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withPw($pw)
    {
        return $this->withProperty(BucketProperties::PW, $pw);
    }

    /**
     * Set the not_found_ok value.
     *
     * @param boolean $ok
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withNotFoundOk($ok)
    {
        return $this->withProperty(BucketProperties::NOTFOUND_OK, $ok);
    }

    /**
     * Add a pre-commit hook.
     * The supplied Function must be an Erlang or Named JS function.
     *
     * @param \Riak\Client\Core\Query\Func\RiakPropertyFunction $hook
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withPrecommitHook(RiakPropertyFunction $hook)
    {
        $this->properties[BucketProperties::PRECOMMIT_HOOKS][] = $hook->jsonSerialize();

        return $this;
    }

    /**
     * Add a post-commit hook.
     * The supplied Function must be an Erlang or Named JS function.
     *
     * @param \Riak\Client\Core\Query\Func\RiakPropertyFunction $hook
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withPostcommitHook(RiakPropertyFunction $hook)
    {
        $this->properties[BucketProperties::POSTCOMMIT_HOOKS][] = $hook->jsonSerialize();

        return $this;
    }

    /**
     * Set the old_vclock value.
     *
     * @param integer $oldVClock
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withOldVClock($oldVClock)
    {
        return $this->withProperty(BucketProperties::OLD_VCLOCK, $oldVClock);
    }

    /**
     * Set the young_vclock value.
     *
     * @param integer $youngVClock
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withYoungVClock($youngVClock)
    {
        return $this->withProperty(BucketProperties::YOUNG_VCLOCK, $youngVClock);
    }

    /**
     * Set the small_vclock value.
     *
     * @param integer $smallVClock
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withSmallVClock($smallVClock)
    {
        return $this->withProperty(BucketProperties::YOUNG_VCLOCK, $smallVClock);
    }

    /**
     * Set the nVal, number of replicas.
     *
     * @param integer $nVal
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withNVal($nVal)
    {
        return $this->withProperty(BucketProperties::N_VAL, $nVal);
    }

    /**
     * Associate a Search Index name to be used.
     *
     * @param string $indexName
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    public function withSearchIndex($indexName)
    {
        return $this->withProperty(BucketProperties::SEARCH_INDEX, $indexName);
    }

    /**
     * Add a property setting for this command.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return \Riak\Client\Command\Bucket\Builder\StoreBucketPropertiesBuilder
     */
    private function withProperty($name, $value)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Build a command object
     *
     * @return \Riak\Client\Command\DataType\StoreBucketProperties
     */
    public function build()
    {
        return new StoreBucketProperties($this->namespace, $this->properties);
    }
}
