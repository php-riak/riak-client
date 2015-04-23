<?php

namespace Riak\Client\Core\Query;

/**
 * Bucket properties used for buckets and bucket types.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class BucketProperties
{
    const R = 'r';
    const W = 'w';
    const PR = 'pr';
    const PW = 'pw';
    const DW = 'dw';
    const RW = 'rw';
    const NAME = 'name';
    const N_VAL = 'nVal';
    const ALLOW_MULT = 'allowMult';
    const LAST_WRITE_WINS = 'lastWriteWins';
    const LINKWALK_FUNCTION = 'linkwalkFunction';
    const CHASH_KEY_FUNCTION = 'chashKeyFunction';
    const PRECOMMIT_HOOKS = 'precommitHooks';
    const POSTCOMMIT_HOOKS = 'postcommitHooks';
    const PRECOMMIT = 'precommit';
    const POSTCOMMIT = 'postcommit';
    const OLD_VCLOCK = 'oldVclock';
    const YOUNG_VCLOCK = 'youngVclock';
    const BIG_VCLOCK = 'bigVclock';
    const SMALL_VCLOCK = 'smallVclock';
    const BASIC_QUORUM = 'basicQuorum';
    const NOTFOUND_OK = 'notfoundOk';
    const SEARCH_INDEX = 'searchIndex';
    const BACKEND = 'backend';
    const SEARCH = 'search';
    const DATATYPE = 'datatype';
    const CONSISTENT = 'consistent';

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var mixed $rw
     */
    private $rw;

    /**
     * @var mixed $rw
     */
    private $dw;

    /**
     * @var mixed $rw
     */
    private $w;

    /**
     * @var mixed $rw
     */
    private $r;

    /**
     * @var mixed $rw
     */
    private $pr;

    /**
     * @var mixed $rw
     */
    private $pw;

    /**
     * @var boolean $notFoundOk
     */
    private $notfoundOk;

    /**
     * @var boolean $basicQuorum
     */
    private $basicQuorum;

    /**
     * @var \Riak\Client\Core\Query\Func\RiakFunction $linkwalkFunction
     */
    private $linkwalkFunction;

    /**
     * @var \Riak\Client\Core\Query\Func\RiakFunction $chashKeyFunction
     */
    private $chashKeyFunction;

    /**
     * @var \Riak\Client\Core\Query\Func\RiakFunction[] $precommitHooks
     */
    private $precommitHooks = [];

    /**
     * @var \Riak\Client\Core\Query\Func\RiakFunction[] $postcommitHooks
     */
    private $postcommitHooks = [];

    /**
     * @var integer $oldVClock
     */
    private $oldVclock;

    /**
     * @var integer $youngVClock
     */
    private $youngVclock;

    /**
     * @var integer $bigVClock
     */
    private $bigVclock;

    /**
     * @var integer $smallVClock
     */
    private $smallVclock;

    /**
     * @var string $backend
     */
    private $backend;

    /**
     * @var integer $nVal
     */
    private $nVal;

    /**
     * @var boolean $lastWriteWins
     */
    private $lastWriteWins;

    /**
     * @var boolean $allowMult
     */
    private $allowMult;

    /**
     * @var boolean $lastWriteWins
     */
    private $search;

    /**
     * @var string $searchIndex
     */
    private $searchIndex;

    /**
     * @var string $datatype
     */
    private $datatype;

    /**
     * @var boolean $consistent
     */
    private $consistent;

    /**
     * @param array $props
     */
    public function __construct(array $props = [])
    {
        foreach ($props as $name => $value) {
            $this->{$name} = $value;
        }
    }

    /**
     * Error handler for unknown property mutator.
     *
     * @param string $name  Unknown property name.
     * @param mixed  $value Property value.
     *
     * @throws \BadMethodCallException
     */
    public function __set($name, $value)
    {
        throw new \InvalidArgumentException(
            sprintf("Unknown property '%s' on '%s'.", $name, get_class($this))
        );
    }

    /**
     * @return integer
     */
    public function getRw()
    {
        return $this->rw;
    }

    /**
     * @return integer
     */
    public function getDw()
    {
        return $this->dw;
    }

    /**
     * @return integer
     */
    public function getW()
    {
        return $this->w;
    }

    /**
     * @return integer
     */
    public function getR()
    {
        return $this->r;
    }

    /**
     * @return integer
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * @return integer
     */
    public function getPw()
    {
        return $this->pw;
    }

    /**
     * @return boolean
     */
    public function getNotFoundOk()
    {
        return $this->notfoundOk;
    }

    /**
     * @return boolean
     */
    public function getBasicQuorum()
    {
        return $this->basicQuorum;
    }

    /**
     * @return \Riak\Client\Core\Query\Func\RiakFunction
     */
    public function getLinkwalkFunction()
    {
        return $this->linkwalkFunction;
    }

    /**
     * @return \Riak\Client\Core\Query\Func\RiakFunction
     */
    public function getChashKeyFunction()
    {
        return $this->chashKeyFunction;
    }

    /**
     * @return array
     */
    public function getPreCommitHooks()
    {
        return $this->postcommitHooks;
    }

    /**
     * @return array
     */
    public function getPostCommitHooks()
    {
        return $this->precommitHooks;
    }

    /**
     * @return integer
     */
    public function getOldVClock()
    {
        return $this->oldVclock;
    }

    /**
     * @return integer
     */
    public function getYoungVClock()
    {
        return $this->youngVclock;
    }

    /**
     * @return integer
     */
    public function getBigVClock()
    {
        return $this->bigVclock;
    }

    /**
     * @return integer
     */
    public function getSmallVClock()
    {
        return $this->smallVclock;
    }

    /**
     * @return string
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @return integer
     */
    public function getNVal()
    {
        return $this->nVal;
    }

    /**
     * @return boolean
     */
    public function getLastWriteWins()
    {
        return $this->lastWriteWins;
    }

    /**
     * @return boolean
     */
    public function getAllowMult()
    {
        return $this->allowMult;
    }

    /**
     * @return boolean
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return string
     */
    public function getSearchIndex()
    {
        return $this->searchIndex;
    }

    /**
     * @return string
     */
    public function getDatatype()
    {
        return $this->datatype;
    }

    /**
     * @return string
     */
    public function getConsistent()
    {
        return $this->consistent;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
