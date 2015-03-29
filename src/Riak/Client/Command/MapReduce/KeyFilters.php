<?php

namespace Riak\Client\Command\MapReduce;

use JsonSerializable;

/**
 * Filter class for building up lists of key filters
 *
 * @method \Riak\Client\Command\MapReduce\KeyFilters intToString() Turns an integer (previously extracted with string_to_int), into a string.
 * @method \Riak\Client\Command\MapReduce\KeyFilters stringToInt() Turns a string into an integer.
 * @method \Riak\Client\Command\MapReduce\KeyFilters floatToString() Turns a floating point number (previously extracted with string_to_float), into a string.
 * @method \Riak\Client\Command\MapReduce\KeyFilters stringToFloat() Turns a string into a floating point number.
 * @method \Riak\Client\Command\MapReduce\KeyFilters toUpper() Changes all letters to uppercase.
 * @method \Riak\Client\Command\MapReduce\KeyFilters tokenize() Splits the input on the string given as the first argument and returns the nth token specified by the second argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters urldecode() URL-decodes the string.
 *
 * @method \Riak\Client\Command\MapReduce\KeyFilters greaterThan(integer $value) Tests that the input is greater than the argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters lessThan(integer $value) Tests that the input is less than the argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters greaterThanEq(integer $value) Tests that the input is greater than or equal to the argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters lessThanEq(integer $value) Tests that the input is less than or equal to the argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters between(integer $start, integer $end, boolean $inclusive) Tests that the input is between the first two arguments. If the third argument is given, it is whether to treat the range as inclusive. If the third argument is omitted, the range is treated as inclusive.
 * @method \Riak\Client\Command\MapReduce\KeyFilters matches(mixed $value) Tests that the input matches the regular expression given in the argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters neq(mixed $value) Tests that the input is not equal to the argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters eq(mixed $value) Tests that the input is equal to the argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters similarTo(string $value, integer $distance) Tests that input is within the Levenshtein distance of the first argument given by the second argument.
 * @method \Riak\Client\Command\MapReduce\KeyFilters startsWith(string $value) Tests that the input begins with the argument (a string).
 * @method \Riak\Client\Command\MapReduce\KeyFilters endsWith(string $value) Tests that the input ends with the argument (a string).
 * @method \Riak\Client\Command\MapReduce\KeyFilters and(KeyFilters $left, KeyFilters $right) Joins two key-filter operations with a logical AND operation.
 * @method \Riak\Client\Command\MapReduce\KeyFilters or(KeyFilters $left, KeyFilters $right) Joins two key-filter operations with a logical OR operation.
 * @method \Riak\Client\Command\MapReduce\KeyFilters not(KeyFilters $filter) Negates the result of key-filter operations.
 *
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 */
class KeyFilters implements JsonSerializable
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * {@inheritdoc}
     */
    public function __call($name, $arguments)
    {
        $underscore = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $name);
        $filter     = strtolower($underscore);

        $this->appendComparison($filter, $arguments);

        return $this;
    }

    /**
     * Tests that the input is contained in the set given as the arguments.
     *
     * @param array $set
     *
     * @return \Riak\Client\Command\MapReduce\Filter\KeyFilter
     */
    public function setMember(array $set)
    {
        return $this->appendComparison('set_member', $set);
    }

    /**
     * @param string $filter
     * @param array  $arguments
     *
     * @return \Riak\Client\Command\MapReduce\Filter\KeyFilter
     */
    private function appendComparison($filter, array $arguments)
    {
        $this->filters[] = array_merge([$filter], $arguments);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->filters;
    }

    /**
     * @return \Riak\Client\Command\MapReduce\Filter\KeyFilter
     */
    public static function filter()
    {
        return new self();
    }
}
