<?php

namespace Riak\Client\Core\Query\Search;

/**
 * Represents a Yokozuna Index in Riak.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class YokozunaIndex
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $schema;

    /**
     * @var integer
     */
    private $nVal;

    /**
     * @param string $name
     * @param string $schema
     */
    public function __construct($name, $schema = null)
    {
        $this->name   = $name;
        $this->schema = $schema;
    }

    /**
     * @param string $name
     */
    public function setName($name)
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
     * @param string $schema
     */
    public function setSchema($schema)
    {
        $this->schema = $schema;
    }

    /**
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return integer
     */
    public function getNVal()
    {
        return $this->nVal;
    }

    /**
     * @param integer $nVal
     */
    public function setNVal($nVal)
    {
        $this->nVal = $nVal;
    }
}
