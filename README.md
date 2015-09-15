# Toggler

[![Build Status](https://travis-ci.org/pierredup/toggler.svg)](https://travis-ci.org/pierredup/toggler)

Toggler is a feature toggle library. It allows you to enable or disable features based on a toggle switch.
This is useful in a continues deployment environment, where you can deploy not-yet-ready features which are disabled, and just enable them when the feature is complete.

## Requirements

Toggler requires PHP 5.4+

## Installation

### Composer

``` bash
$ composer require pierredup/toggler:~1.0
```

## Usage

### Config

To configure Toggler, you need to set the feature flags with a truthy value if it should be enabled

``` php
$features = [
    'foo' => true,
    'bar' => false
];

toggleConfig($features);
```

You can also pass through a path to a PHP file, which should return an array with the confg:

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

To enable a feature, any of the following truthy values are accepted:

* (boolean) true
* (int) 1
* '1'
* 'on'
* 'true'

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

An optional third callback argument can be passed which whil be called if the feature is not enabled

``` php
    toggle(
            'foobar',
            function () {
                /* will NOT be executed when feature 'foobar' is disabled */
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

## Twig Integration

Toggler comes with an optional Twig extension, which allows you to toggle elements from Twig templates.

To use the extension, register it with Twig

``` php
use Toggler\Twig\Extension\ToggleExtension;

$twig = new Twig_Environment($loader);
$twig->addExtension(new ToggleExtension());
```

or if you use symfony, register it as a service

``` yaml
toggle.twig.extension:
    class: Toggler\Twig\Extension\ToggleExtension
    tags:
        - { name: twig.extension }
```

Then you can use the `toggle` tag in twig templates:

``` twig
{% toggle 'foo' %}
    Some content that will only display if foo is truthy
{% endtoggle %}
```

To add an alternaltive if a feature is not available, use the `else` tag

``` twig
{% toggle 'foo' %}
    Some content that will only display if foo is truthy
{% else %}
    Some content that will only display if foo is not enabled
{% endtoggle %}
```

You can also use the `toggle()` function for conditions

``` twig
{{ toggle('foo') ? 'Foo is enabled' : 'Foo is NOT enabled' }}
```

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
