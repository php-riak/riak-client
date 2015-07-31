# Riak Client for PHP

[![Build Status](https://travis-ci.org/php-riak/riak-client.svg?branch=master)](https://travis-ci.org/php-riak/riak-client)
[![Coverage Status](https://coveralls.io/repos/php-riak/riak-client/badge.svg?branch=master)](https://coveralls.io/r/php-riak/riak-client?branch=master)

A PHP client for [Riak](http://basho.com/riak/).


## Installation

Run the following `composer` command:

```console
$ composer require "php-riak/riak-client"
```

## Documentation
API documentation for this library can be found on [readthedocs](http://riak-client.readthedocs.org/en/latest/)

## Overview


## Getting started with the php riak client.

The easiest way to get started with the client is using a `RiakClientBuilder` :

```php
use Riak\Client\RiakClientBuilder;

$builder = new RiakClientBuilder();
$client  = $builder
    ->withNodeUri('proto://192.168.1.1:8087')
    ->withNodeUri('proto://192.168.1.2:8087')
    ->withNodeUri('proto://192.168.1.3:8087')
    ->build();

```

Once you have a $client, commands from the `Riak\Client\Command*` namespace are built then executed by the client.

Some basic examples of building and executing these commands is shown
below.

## Getting Data In

```php
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;

$object    = new RiakObject();
$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

$object->setValue('[1,1,1]');
$object->setContentType('application/json');

// store object
$store    = StoreValue::builder($location, $object)
    ->withPw(1)
    ->withW(2)
    ->build();

$client->execute($store);
```

## Getting Data Out

```php
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;

$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

// fetch object
$fetch  = FetchValue::builder($location)
    ->withNotFoundOk(true)
    ->withR(1)
    ->build();

$result = $client->execute($fetch);
$object = $result->getValue();
```

## Removing Data

```php
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;

$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

// delete object
$delete  = DeleteValue::builder($location)
    ->withPw(1)
    ->withW(2)
    ->build();

$this->client->execute($delete);
```

## Merging siblings

Siblings are an important feature in Riak,
for this purpose we have the interface `Riak\Client\Resolver\ConflictResolver`,
you implement this interface to resolve  siblings into a single object.

Below is a simple example that will just merge the content of siblings :
```php
use \Riak\Client\Resolver\ConflictResolver;
use \Riak\Client\Core\Query\RiakObject;
use \Riak\Client\Core\Query\RiakList;

class MySimpleResolver implements ConflictResolver
{
    /**
     * {@inheritdoc}
     */
    public function resolve(RiakList $siblings)
    {
        $result  = new MyDomainObject();
        $content = "";

        /** @var $object \MyDomainObject */
        foreach ($siblings as $object) {
            $content .= $object->getValue();
        }

        $result->setValue($content);

        return $result;
    }
}

// register your resolver during the application start up
/** @var $builder \Riak\Client\RiakClientBuilder */
$client = $builder
    ->withConflictResolver('MyDomainObject' new MySimpleResolver())
    ->withNodeUri('http://localhost:8098')
    ->build();

// fetch the object and resolve any possible conflict
$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');
$fetch     = FetchValue::builder($location)
    ->withNotFoundOk(true)
    ->withR(1)
    ->build();

/** @var $domain \MyDomainObject */
$result = $client->execute($fetch);
$domain = $result->getValue('MyDomainObject');
```

### Performance

This library is faster than most riak clients written in php,
It is about 50% percent faster is some cases, mostly because it uses protocol buffer and and iterators every where it is possible.

For more details and riak clients performance comparison see : https://github.com/FabioBatSilva/riak-clients-performance-comparison

### Unit & Integration Tests

We want to ensure that all code that is included in a release has proper coverage with unit tests.
It is expected that all pull requests that include new classes or class methods have appropriate unit tests included with the PR.

#### Running Tests

Before running the functional tests set up your riak cluster:

* Create and activate the following types :
```
riak-admin bucket-type create counters '{"props":{"datatype":"counter"}}'
riak-admin bucket-type create maps '{"props":{"datatype":"map"}}'
riak-admin bucket-type create sets '{"props":{"datatype":"set"}}'
riak-admin bucket-type activate counters
riak-admin bucket-type activate maps
riak-admin bucket-type activate sets
```

* Enable search capabilities in your ``riak.conf``:
```
search = on
```

We also expect that before submitting a pull request, that you have run the tests to ensure that all of them
continue to pass after your changes.

To run the tests, clone this repository and run `composer update` from the repository root, then you can execute all the tests by simply running
`php vendor/bin/phpunit`.

* To execute tests, run `./vendor/bin/phpunit`
* To check code standards, run `./vendor/bin/phpcs -p --extensions=php  --standard=ruleset.xml src`
