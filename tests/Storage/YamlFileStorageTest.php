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
use org\bovigo\vfs\vfsStreamDirectory;
use SolidWorx\Toggler\Storage\YamlFileStorage;

class YamlFileStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    protected function setUp()
    {
        $this->root = vfsStream::setup('exampleDir');
    }

    public function testInvalidFile()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The file /non/existent/file.yml either does not exist, or is not readable');
        new YamlFileStorage('/non/existent/file.yml');
    }

    public function testGet()
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

    public function testSet()
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
}