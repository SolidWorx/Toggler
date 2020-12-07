# Toggler

![Test Suite](https://github.com/SolidWorx/Toggler/workflows/Toggler%20Test%20Suite/badge.svg)
[![codecov](https://codecov.io/gh/SolidWorx/Toggler/branch/master/graph/badge.svg?token=47opWxkfvV)](https://codecov.io/gh/SolidWorx/Toggler)

Toggler is a feature toggle library for PHP. It allows you to enable or disable features based on a toggle switch.
This is useful in a continues deployment environment, where you can deploy not-yet-ready features which are disabled, and just enable them when the feature is complete.

# Table of Contents
- [Requirements](#requirements)
- [Installation](#installation)
    - [Composer](#composer)
- [Usage](#usage)
    - [StorageFactory](#config)
    - [Toggle a feature](#toggle-a-feature)
    - [Toggle a feature based on context](#toggle-a-feature-based-on-context)
    - [Using Symfony Expression Language](#using-symfony-expression-language)
    - [Custom storage to retrieve feature settings](#custom-storage-to-retrieve-feature-settings)
- [Twig integration](#twig-integration)
- [Symfony integration](#symfony-integration)
- [Testing](#testing)
- [Contributing](#contributing)
- [Licence](#licence)


## Requirements

Toggler requires PHP 7.1+ and Symfony 4.0+

## Installation

### Composer

```bash
$ composer require solidworx/toggler:^2.0
```

## Usage

### Quick Example

```php
<?php

use SolidWorx\Toggler\Toggle;
use SolidWorx\Toggler\Storage\ArrayStorage;

$features = [
    'foo' => true,
    'bar' => false
];

$toggle = new Toggle(new ArrayStorage($features));
```

You can then check if a feature is active or not using the `isActive` call

```php
<?php

$toggle->isActive('foo'); // true
$toggle->isActive('bar'); // false
```

## StorageFactory

Toggler comes with many storage adapters to store the configuration. The most basic is the `ArrayStorage` class, which takes an array of features.

The `StorageFactory` class acts as a factory to create the config. You can pass it any value, and it will determine which storage adapter to use.
To get an instance of the config, you can  use the static `factory` method

```php
<?php

use SolidWorx\Toggler\Storage\StorageFactory;

$features = [
    'foo' => true,
    'bar' => false
];

$config = StorageFactory::factory($features); // $config will be an instance of ArrayStorage

// Using YAML
$config = StorageFactory::factory('/path/to/config.yml'); // $config will be an instance of YamlFileStorage
```

Each feature flag need to be a truthy value in order to be enabled.

The following truthy values are accepted:

* (boolean) true
* (int) 1
* '1'
* 'on'
* 'yes'
* 'y'

### Using callbacks

You can also use closures or callbacks to retrieve the value

```php
<?php

$features = [
    'foo' => function () {
        return true;
    },
    'bar' => [$myObject, 'checkBar']
];
```

## Storage Adapters

Toggler supports various storage adapters to store the config.

### Array

The most basic config is using an array with the `ArrayStorage` adapter.

```php
<?php

use SolidWorx\Toggler\Storage\StorageFactory;
use SolidWorx\Toggler\Storage\ArrayStorage;
use SolidWorx\Toggler\Toggle;

$features = [
    'foo' => true,
    'bar' => false
];

$toggle = new Toggle(new ArrayStorage($features));

// Or using the StorageFactory factory
$toggle = new Toggle(StorageFactory::factory($features));
```

### YAML

In order to use yml files for config, you need to include the [Symfony Yaml Component](http://symfony.com/doc/current/components/yaml/index.html)

To install and use the Yaml component, run the following command from the root of your project:

```bash
$ composer require symfony/yaml
```

Then you can define your config using a yaml file

```php
// config.yml
foo: true
bar: false
```

Pass the path to the yml file to your config

```php
<?php

use SolidWorx\Toggler\Storage\StorageFactory;
use SolidWorx\Toggler\Storage\YamlFileStorage;
use SolidWorx\Toggler\Toggle;

$toggle = new Toggle(new YamlFileStorage('/path/to/config.yml'));

// Or using the StorageFactory factory
$toggle = new Toggle(StorageFactory::factory('/path/to/config.yml'));
```

### PHP File

You can store your config in a separate PHP file.
This fille needs to return an array with the config.
By default, PHP files always use the `ArrayStorage` adapter.

```php
<?php

// config.php
return[
    'foo' => true,
    'bar' => false,
];
```

Pass the path to the PHP file to your config

```php
<?php

use SolidWorx\Toggler\Storage\StorageFactory;
use SolidWorx\Toggler\Toggle;

$toggle = new Toggle(StorageFactory::factory('/path/to/config.php'));
```

### Redis

You can use [Redis](https://redis.io/) to store the configs.

You will then need to either install the [Predis](https://github.com/nrk/predis) library or the [Redis PHP](https://github.com/phpredis/phpredis) extension.

To install Predis, run the following command from the root of your project:

```bash
$ composer require predis/predis
```

The `RedisStorage` adapter can take any class instance of `Redis`, `RedisArray`, `RedisCluster` or `Predis\Client`.

```php
<?php

use SolidWorx\Toggler\Storage\RedisStorage;
use SolidWorx\Toggler\Toggle;

$redis = new \Redis();
$toggle = new Toggle(new RedisStorage($redis));
```

## Persistent Storage

Toggler supports persisting config values if a storage adapter implements the ` SolidWorx\Toggler\Storage\PersistenStorageInterface`.

The following storage adapters currently supports persisting config values:

* YamlFileStorage
* RedisStorage

To update a feature, use the `set` method:

```php
<?php

$toggle->set('foo', true); // This will enable the foo feature
$toggle->set('bar', false); // This will disable the bar feature
```

## Toggle a feature based on context

To enable a feature only under specific conditions (E.G only enable it for users in a certain group, or only enable it for 10% of visitor etc)

Each feature in the config can take a callback, where you can return a truthy value based on any logic you want to add:

```php
<?php

$features = [
    'foo' => function (User $user) {
        return in_array('admin', $user->getGroups()); // Only enable features for users in the 'admin' group
    },
    'bar' => function () {
        return  (crc32($_SERVER['REMOTE_ADDR']) % 100) < 25; // Only enable this features for about 25% of visitors
    },
    'baz' => function (Request $request) {
        return false !== strpos($request->headers->get('referer'), 'facebook.com'); // Only enable this features for users that come from Facebook
    }
];
```

Callbacks that takes any arguments, should be called with the context:

```php
<?php

$user = User::find(); // Get the current logged-in user

if ($toggle->isActive('foo', [$user])) {
    
}

if ($toggle->isActive('bar', [$request])) {
    
}
```

## Using Symfony Expression Language

You can use the [Symfony Expression Language Component](http://symfony.com/doc/current/components/expression_language/index.html) to create expressions for your features.

To install and use the Expression Language component, run the following command from the root of your project:

```bash
$ composer require symfony/expression-language
```

Then you can create an expression for your feature:

```php
<?php

use Symfony\Component\ExpressionLanguage\Expression;

$feaures = [
    'foo' => new Expression('valueOne > 10 and valueTwo < 10')
];
```

When checking the feature, you need to pass the context to use in your expression:

```php
<?php

if ($toggle->isActive('foo', ['valueOne' => 25, 'valueTwo' => 5])) { // Will return true
    
}
```

## Twig Integration

Toggler comes with an optional Twig extension, which allows you to toggle elements from Twig templates.

To use the extension, register it with Twig

```php
<?php

use SolidWorx\Toggler\Twig\Extension\ToggleExtension;

$twig = new \Twig_Environment($loader);
$twig->addExtension(new ToggleExtension($toggle));
```

or if you use symfony, register it as a service.

**Note:** When using the [Symfony Bundle](Symfony Integration), the twig extension is automatically registered.

Then you can use the `toggle` tag in twig templates:

```twig
{% toggle 'foo' %}
    Some content that will only display if foo is enabled
{% endtoggle %}
```

To add an alternaltive if a feature is not available, use the `else` tag

```twig
{% toggle 'foo' %}
    Some content that will only display if foo is enabled
{% else %}
    Some content that will only display if foo is not enabled
{% endtoggle %}
```

To use context values with the tag, you can pass it using the `with` keyword:

```twig
{% toggle 'foo' with {"valueOne" : 12} %}
    Some content that will only display if foo is enabled based on the context provided
{% endtoggle %}
```

You can also use the `toggle()` function for conditions

```twig
{{ toggle('foo') ? 'Foo is enabled' : 'Foo is NOT enabled' }}
```

## Symfony Integration

Toggler comes with integration with the [Symfony](http://symfony.com/) framework.

To enable toggler inside symfony, register the bundle

```php
// AppKernel.php

$bundles = array(
   ...
   new SolidWorx\Toggler\Symfony\TogglerBundle(),
   ...
);
```

Then inside your `app/config/config.yml` or `app/config/config_dev.yml`, you can enable features using the following config

```yaml
toggler:
    config:
        features:
            foo: true
            bar: false
            
            # Callables is also supported
            baz: '@my.service.class' # Class must be callable (I.E implement the __invoke() method)
            foobar: ['@my.service.class', 'foobar'] # Will call the `foobar` method on the service class
            baz: ['My\Awesome\Feature\Class', 'checkFeature'] # Will call the static method `checkFeature` on the `My\Awesome\Feature\Class` class
            
            # The last two lines can be written as the following:
            foobar: '@my.service.class::foobar'
            baz: 'My\Awesome\Feature\Class::checkFeature'
```

If you want to use an expression for a feature config, you can use the `@=` syntax:

```yaml
toggler:
    config:
        features:
            foo: '@=myValue > 10'
```

If you want to use a storage class, you can use the `storage` config parameter to define a service for the storage:

```yaml
services:
    my.toggler.storage:
        class: SolidWorx\Toggler\Storage\RedisStorage
        arguments: ['@redis']
        
toggler:
    config:
        storage: '@my.toggler.storage'
```
**Note:** The `features` and `storage` options can't be used together. You must use either the one or the other. At least one of the two must be defined.

**Note:** When using the Symfony bundle, the twig extension is automatically registered.

**Note:** The `symfony/security-core` package is required with `symfony/framework-bundle`.

### Console Commands

The Symfony Bundle comes with 2 pre-registered console commands.

#### Get the status of a feature

To see if a feature is enabled or not, run the following command

```bash
$ php bin/console toggler:get foo
```

This will output the status of a feature.

You can also get the status of multiple features by passing in multiple values:

```bash
$ php bin/console toggler:get foo bar baz
```

This will show whether the features `foo`, `bar` and `baz` is enabled or not.

##### Get the value using context values

To test if a feature will be enabled under certain conditions, you can pass context values to the command using either the `-c` or `--context` flags.
Multiple values for the context can be provided.

**Note:** Context values can only be strings. Objects are not supported.

```bash
$ php bin/console toggler:get foo -c myValue=10 -c anotherValue=25
```

#### Set the value of a feature

You can enable or disable a feature using the `toggler:set` command.

**Note:** You can only change the status of a feature if you are using a persistent storage.

```bash
$ php bin/console toggler:set foo true
```

This will enable the `foo` feature.

# Testing

To run the unit tests, execute the following command

```bash
$ vendor/bin/phpunit
```

## Contributing

See [CONTRIBUTING](https://github.com/SolidWorx/Toggler/blob/master/CONTRIBUTING.md)

## License

Toggler is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

Please see the [LICENSE](LICENSE) file for the full license.
