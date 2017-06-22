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

namespace SolidWorx\Tests\Toggler\Storage;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\{
    ArrayStorage,
    StorageFactory,
    StorageInterface,
    YamlFileStorage
};
use Symfony\Component\Yaml\Yaml;

class StorageFactoryTest extends TestCase
{
    private $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    public function testFactory()
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
            ->withContent('<?php return '.var_export($features, true).';')
            ->at($this->root);

        $this->assertInstanceOf(StorageInterface::class, StorageFactory::factory(new ArrayStorage($features)));
        $this->assertInstanceOf(ArrayStorage::class, StorageFactory::factory($features));
        $this->assertInstanceOf(YamlFileStorage::class, StorageFactory::factory($yamlFile->url()));
        $this->assertInstanceOf(ArrayStorage::class, StorageFactory::factory($phpFile->url()));
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

        StorageFactory::factory($file->url());
    }

    public function testInvalidConfigType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The 1st argument for '.StorageFactory::class.'::factory expects an array, string or instance of StorageInterface, boolean given');

        StorageFactory::factory(true);
    }
}
