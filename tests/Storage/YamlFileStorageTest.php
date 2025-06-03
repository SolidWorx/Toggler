<?php

declare(strict_types=1);

/*
 * This file is part of SolidWorx Toggler project.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidWorx\Toggler\Tests\Storage;

use InvalidArgumentException;
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
        $this->expectException(InvalidArgumentException::class);
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

        $this->assertTrue($storage->get('foo'));
        $this->assertTrue($storage->get('bar'));
        $this->assertFalse($storage->get('baz'));
        $this->assertFalse($storage->get('foobar'));
        $this->assertNull($storage->get('foobarbaz'));
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

        $this->assertTrue($storage->get('foo'));
        $this->assertNull($storage->get('foobarbaz'));

        $storage->set('foo', false);
        $storage->set('foobarbaz', true);

        $this->assertFalse($storage->get('foo'));
        $this->assertTrue($storage->get('foobarbaz'));
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

        $this->assertSame(['foo', 'bar', 'baz', 'foobar'], $storage->all());
    }
}
