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

namespace SolidWorx\Toggler\Tests\Storage;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use SolidWorx\Toggler\Storage\YamlFileStorage;

class YamlFileStorageTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    protected function setUp(): void
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    public function testInvalidFile(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The file /non/existent/file.yml either does not exist, or is not readable');
        new YamlFileStorage('/non/existent/file.yml');
    }

    public function testGet(): void
    {
        $features = 'foo: true
bar: true
baz: false
foobar: false';

        $file = vfsStream::newFile('file.yml')
            ->withContent($features)
            ->at($this->root);

        $storage = new YamlFileStorage($file->url());

        self::assertTrue($storage->get('foo'));
        self::assertTrue($storage->get('bar'));
        self::assertFalse($storage->get('baz'));
        self::assertFalse($storage->get('foobar'));
        self::assertNull($storage->get('foobarbaz'));
    }

    public function testSet(): void
    {
        $features = 'foo: true
bar: true
baz: false
foobar: false';

        $largeFile = vfsStream::newFile('large.txt')
            ->withContent($features)
            ->at($this->root);

        $storage = new YamlFileStorage($largeFile->url());

        self::assertTrue($storage->get('foo'));
        self::assertNull($storage->get('foobarbaz'));

        $storage->set('foo', false);
        $storage->set('foobarbaz', true);

        self::assertFalse($storage->get('foo'));
        self::assertTrue($storage->get('foobarbaz'));
    }

    public function testAll(): void
    {
        $features = 'foo: true
bar: true
baz: false
foobar: false';

        $largeFile = vfsStream::newFile('large.txt')
            ->withContent($features)
            ->at($this->root);

        $storage = new YamlFileStorage($largeFile->url());

        self::assertSame(['foo', 'bar', 'baz', 'foobar'], $storage->all());
    }
}
