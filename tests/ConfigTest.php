<?php

declare(strict_types=1);

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Tests\Toggler;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Config;
use SolidWorx\Toggler\Storage\ArrayStorage;
use SolidWorx\Toggler\Storage\PersistenStorageInterface;
use SolidWorx\Toggler\Storage\StorageInterface;
use SolidWorx\Toggler\Storage\YamlFileStorage;
use Symfony\Component\Yaml\Yaml;

class ConfigTest extends TestCase
{
    private $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    public function testSetConfig()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $yamlFile = vfsStream::newFile('file.yml')
            ->withContent(Yaml::dump($features))
            ->at($this->root);

        $phpFile = vfsStream::newFile('file.php')
            ->withContent('<?php return ' . var_export($features, true) . ';')
            ->at($this->root);

        $this->assertInstanceOf(StorageInterface::class, $this->readAttribute(new Config(new ArrayStorage($features)), 'config'));
        $this->assertInstanceOf(ArrayStorage::class, $this->readAttribute(new Config($features), 'config'));
        $this->assertInstanceOf(YamlFileStorage::class, $this->readAttribute(new Config($yamlFile->url()), 'config'));
        $this->assertInstanceOf(ArrayStorage::class, $this->readAttribute(new Config($phpFile->url()), 'config'));
    }

    public function testInvalidConfigFile()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $file = vfsStream::newFile('file.txt')
            ->withContent(Yaml::dump($features))
            ->at($this->root);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('File with extension txt is not supported');

        new Config($file->url());
    }

    public function testInvalidConfigType()
    {

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The 1st argument for SolidWorx\Toggler\Config::factory expects an array, string or instance of StorageInterface, boolean given');

        new Config(true);
    }

    public function testGet()
    {
        $features = [
            'foo' => true,
            'bar' => true,
            'baz' => false,
            'foobar' => false,
        ];

        $config = new Config($features);

        $this->assertTrue($config->get('foo'));
        $this->assertTrue($config->get('bar'));
        $this->assertFalse($config->get('baz'));
        $this->assertFalse($config->get('foobar'));
    }

    public function testSetException()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot change the value for feature foo as storage SolidWorx\Toggler\Storage\ArrayStorage does not implement PersistenStorageInterface');

        $features = [
        ];

        $config = new Config($features);

        $config->set('foo', true);
    }

    public function testSet()
    {
        $storage = $this->createMock(PersistenStorageInterface::class);

        $storage->expects($this->once())
            ->method('set')
            ->with('foo', true)
            ->willReturn(true);

        $config = new Config($storage);

        $this->assertTrue($config->set('foo', true));
    }
}
