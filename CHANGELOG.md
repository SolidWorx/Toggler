2.0.0
=====

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
