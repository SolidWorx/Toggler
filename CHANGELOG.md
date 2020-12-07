2.0.0 Alpha 3
=============

  * Add support for classes that implements a __toString method
  * Add support for "yes", "y", "no" and "n" checks
  * Bump up minimum twig version (#13)
  * Add support for Symfony 5 (#12)
  * Drop support for Symfony 3 (#12)
  * Add code coverage (#16)
  * Migrate from Travis-CI to Github Actions (#14)

2.0.0 Alpha 2
=============

  * Introducing ability to specify a namespace for redis storage (#10)
  * Change Config class to StorageFactory (#6)
  * Update Travis config to test on different environments (#8)
  * Update PHPUnit to the latest version
  * Remove HHVM support
  * Change `Config` class to `StorageFactory`
  * Remove the `get`, `set` and `__construct` methods in the `StorageFactory` class
  * Move the `StorageFactory` class to the `SolidWorx\Toggler\Storage` namespace

2.0.0 Alpha 1
=============

  * Add a Symfony command to update the status of a feature
  * Added Symfony command to get the status of a feature
  * Added PersistenStorageInterface to identify storage adapters that can persist the storage
  * Removed functions
  * Don't memoize callables and expressions
  * Update Toggle to take an instance of an ExpressionLanguage class
  * Created a factory method for Config
  * Added ArrayStorage class, update Toggle to accept a StorageInterface instead of a Config, and made Config a factory for any storage
  * Added YamlFileStorage
  * Added RedisStorage
  * Remove static methods and moved config into the Config class
  * Drop support for PHP 5 and PHP 7.0
  * Add a New Namespace
