# Toggler

[![Build Status](https://travis-ci.org/pierredup/toggler.svg)](https://travis-ci.org/pierredup/toggler)

Toggler is a feature toggle library for PHP. It allows you to enable or disable features based on a toggle switch.
This is useful in a continues deployment environment, where you can deploy not-yet-ready features which are disabled, and just enable them when the feature is complete.

# Table of Contents
- [Requirements](#requirements)
- [Installation](#installation)
    - [Composer](#composer)
- [Usage](#usage)
    - [Config](#config)
    - [Toggle a feature](#toggle-a-feature])
    - [Toggle a feature based on context](#toggle-a-feature-based-on-context)
    - [Using Symfony Expression Language](#using-symfony-expression-language)
    - [Custom storage to retrieve feature settings](#custom-storage-to-retrieve-feature-settings)
- [Twig integration](#twig-integration)
- [Symfony integration](#symfony-integration)
- [Testing](#testing)
- [Contributing](#contributing)
- [Licence](#licence])


## Requirements

Toggler requires PHP 5.4+

## Installation

### Composer

``` bash
$ composer require pierredup/toggler:~1.0
```

## Usage

### Config

To configure Toggler, you need to set the feature flags with a truthy value if it should be enabled.

To enable a feature, any of the following truthy values are accepted:

* (boolean) true
* (int) 1
* '1'
* 'on'
* 'true'

The config needs to be an array with the name of the feature as the key, and a truthy value, callback or [expression](#using-symfony-expression-language) as the value.

``` php
$features = [
    'foo' => true,
    'bar' => false
];

toggleConfig($features);
```

You can also pass through a path to a PHP file which should return an array with the config:

``` php
// config.php
return [
    'foo' => true,
    'bar' => false
];
```

``` php
toggleConfig('/path/to/config.php');
```

#### Using YAML files

In order to use yml files for config, you need to include the [Symfony Yaml Component](http://symfony.com/doc/current/components/yaml/index.html)

To install and use the Yaml component, run the following command from the root of your project:

```bash
$ composer require symfony/yaml
```

Then you can define your config using a yaml file

``` php
// config.yml
foo: true
bar: false
```

Pass the path to the yml file to your config

``` php
toggleConfig('/path/to/config.yml');
```

### Toggle a feature

To toggle a feature, use the `toggle` function, which takes the feature name as the first argument, and a callback as the second argument.
An optional third callback argument can be passed which whill be called if the

``` php
toggle(
    'foobar',
    function () {
        /* will be executed when feature 'foobar' is enabled */
    }
);
```

An optional third callback argument can be passed which will be called if the feature is not enabled

``` php
toggle(
    'foobar',
        function () {
            /* will be executed when feature 'foobar' is enabled */
        },
        function () {
            /* will be executed when feature 'foobar' is disabled */
        }
    );
```

You can also use the `toggle` function as a conditional

``` php
if (toggle('foobar')) {
    /* will be executed when feature 'foobar' is enabled */
}
```

### Toggle a feature based on context

To enable a feature only under specific conditions (E.G only enable it for users in a certain group, or only enable it for 10% of visitor etc)

Each feature in the config can take a callback, where you can return a truthy value based on any logic you want to add:

``` php
$features = [
    'foo' => function (User $user) {
        return in_array('admin', $user->getGroups()); // Only enable features for users in the 'admin' group
    },
    'bar' => function () {
        return  (crc32($_SERVER['REMOTE_ADDR']) % 100) < 25 // Only enable this features for about 25% of visitors
    }
];
```

Callbacks that takes any arguments, should be called with the context:

``` php
$user = User::find(); // Get the current logged-in user
if (toggle('foo', [$user])) {
}
```

or if you want to use callback functions, the context can always be sent as the last parameter:

``` php
$user = User::find(); // Get the current logged-in user
toggle('foo', function () { /* enable feature */ }, [$user]);
```

### Using Symfony Expression Language

You can use the [Symfony Expression Language Component](http://symfony.com/doc/current/components/expression_language/index.html) to create expressions for your features.

To install and use the Expression Language component, run the following command from the root of your project:

```bash
$ composer require symfony/expression-language
```

Then you can create an expression for your feature:

```php
use Symfony\Component\ExpressionLanguage\Expression;

$feaures = [
    'foo' => new Expression('valueOne > 10 and valueTwo < 10')
];
```

When checking the feature, you need to pass the context to use in your expression:

```php
toggle('foo', ['valueOne' => 25, 'valueTwo' => 5]); // Will return true
```

### Custom storage to retrieve feature settings

If you want to store your config in a different place than an array or config file (E.G MySQL database or redis etc) then you can create a storage class that implements `Toggle\Storage\StorageInterface` which can be used to retrieve your config

``` php
use Toggle\Storage\StorageInterface;

class RedisStorage implements StorageInterface
{
    private $redis;

    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function get($key) // This method comes from StorageInterface and needs to be implemented with your custom logic to retrieve values
    {
        return $this->redis->get($key);
    }
}
```

Then you can pass an instance of your class to the `toggleConfig` function

``` php
$redis = ...; // Get your redis instance
toggleConfig(new RedisStorage($redis));
```

## Twig Integration

Toggler comes with an optional Twig extension, which allows you to toggle elements from Twig templates.

To use the extension, register it with Twig

``` php
use Toggler\Twig\Extension\ToggleExtension;

$twig = new Twig_Environment($loader);
$twig->addExtension(new ToggleExtension());
```

or if you use symfony, register it as a service
**Note:** When using the Symfony bundle, the twig extension is automatically registered.

``` yaml
toggle.twig.extension:
    class: Toggler\Twig\Extension\ToggleExtension
    tags:
        - { name: twig.extension }
```

Then you can use the `toggle` tag in twig templates:

``` twig
{% toggle 'foo' %}
    Some content that will only display if foo is enabled
{% endtoggle %}
```

To add an alternaltive if a feature is not available, use the `else` tag

``` twig
{% toggle 'foo' %}
    Some content that will only display if foo is enabled
{% else %}
    Some content that will only display if foo is not enabled
{% endtoggle %}
```

To use context values with the tag, you can pass it using the `with` keyword:

``` twig
{% toggle 'foo' with [{"valueOne" : 12}] %}
    Some content that will only display if foo is enabled based on the context provided
{% endtoggle %}
```

You can also use the `toggle()` function for conditions

``` twig
{{ toggle('foo') ? 'Foo is enabled' : 'Foo is NOT enabled' }}
```

## Symfony Integration

Toggler comes with basic integration with the [Symfony](http://symfony.com/) framework.
To enable toggler inside symfony, register the bundle

``` php
// AppKernel.php

$bundles = array(
   ...
   new Toggler\Symfony\TogglerBundle(),
   ...
);
```

Then inside your `app/config/config.yml` or `app/config/config_dev.yml`, you can enable features using the following config

``` yaml
toggler:
    config:
        foo: true
        bar: false
```

If you want to use an expression for a feature config, you can use the `@=` syntax:

``` yaml
toggler:
    config:
        foo: @=myValue > 10
```

If you want to use a storage class, you can use the service id as the argument for the config:

``` yaml
services:
    my.toggler.service:
        class: My\Bundle\DbStorage
        arguments: [@doctrine]
        
toggler:
    config: my.toggler.service
```

**Note:** When using the Symfony bundle, the twig extension is automatically registered.

## Testing

To run the unit tests, execute the following command

``` bash
$ vendor/bin/phpunit
```

## Contributing

See [CONTRIBUTING](https://github.com/pierredup/toggler/blob/master/CONTRIBUTING.md)

## License

Toggler is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

Please see the [LICENSE](LICENSE) file for the full license.
