# Riak Client for PHP [![Build Status](https://secure.travis-ci.org/FabioBatSilva/riak-client.png?branch=develop)](http://travis-ci.org/FabioBatSilva/riak-client)

A PHP client for [Riak](http://basho.com/riak/).

**NOTICE: THIS CLIENT IS UNDER ACTIVE DEVELOPMENT AND SHOULD NOT BE USED IN PRODUCTION AT THIS TIME**


## Installation

### Composer Install (recommended)

Run the following `composer` command:

```console
$ composer require "fbs/riak-client"
```

Alternately, manually add the following to your `composer.json`, in the `require` section:

```javascript
"require": {
    "FabioBatSilva/riak-client": "dev-master"
}
```

## Documentation
API documentation for this library can be found on [Wiki](https://github.com/FabioBatSilva/riak-php-client/wiki)

## Overview


## Getting started with the php riak client.

The easiest way to get started with the client is using a `RiakClientBuilder` :

```php
use Riak\Client\RiakClientBuilder;

$builder = new RiakClientBuilder();
$client  = $builder
    ->withNodeUri('http://192.168.1.1:8098')
    ->withNodeUri('http://192.168.1.2:8098')
    ->withNodeUri('http://192.168.1.3:8098')
    ->build();

```

Once you have a $client, commands from the `Riak\Client\Command*` namespace are built then executed by the client.

Some basic examples of building and executing these commands is shown
below.

## Getting Data In

```php
use Riak\Client\Cap\RiakOption;
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
    ->withOption(RiakOption::PW, 1)
    ->withOption(RiakOption::W, 2)
    ->build();

$client->execute($store);
```

## Getting Data Out

```php
use Riak\Client\Cap\RiakOption;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;

$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

// fetch object
$fetch  = FetchValue::builder($location)
    ->withOption(RiakOption::NOTFOUND_OK, true)
    ->withOption(RiakOption::R, 1)
    ->build();

$result = $client->execute($fetch);
$object = $result->getValue();
```

## Removing Data

```php
use Riak\Client\Cap\RiakOption;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;

$namespace = new RiakNamespace('bucket_name', 'bucket_type');
$location  = new RiakLocation($namespace, 'object_key');

// delete object
$delete  = DeleteValue::builder($location)
    ->withOption(RiakOption::PW, 1)
    ->withOption(RiakOption::W, 2)
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
    ->withOption(RiakOption::NOTFOUND_OK, true)
    ->withOption(RiakOption::R, 1)
    ->build();

/** @var $domain \MyDomainObject */
$result = $client->execute($fetch);
$domain = $result->getValue('MyDomainObject');
```

### Unit & Integration Tests

We want to ensure that all code that is included in a release has proper coverage with unit tests.
It is expected that all pull requests that include new classes or class methods have appropriate unit tests included with the PR.

#### Running Tests

We also expect that before submitting a pull request, that you have run the tests to ensure that all of them
continue to pass after your changes.

To run the tests, clone this repository and run `composer update` from the repository root, then you can execute all the tests by simply running
`php vendor/bin/phpunit`.

* To execute tests, run `./vendor/bin/phpunit`
* To check code standards, run `./vendor/bin/phpcs -p --extensions=php  --standard=ruleset.xml src`
