# Toggler


Toggler is a feature toggle library. It allows you to enable or disable features based on a toggle switch.
This is useful in a continues deployment environment, where you can deploy not-yet-ready features which are disabled, and just enable them when the feature is complete.

## Requirements

Toggler requires PHP 5.4+

## Installation

Composer

``` json
{
    "require": {
        "pierredup/toggler": "~1.0"
    }
}
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