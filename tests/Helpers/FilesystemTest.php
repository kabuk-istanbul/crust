<?php

namespace Crust\Tests\Helpers;

use Crust\Helpers\Filesystem;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{

    public function testInstance()
    {
        $filesystem = new Filesystem();
        $this->assertInstanceOf(Filesystem::class, $filesystem);
    }

    public function testFileCreation()
    {
        $filesystem = new Filesystem();

        $file = './test.php';
        $filesystem->file($file);
        $this->assertTrue(file_exists($file), "creates file.");
        $filesystem->remove($file);
        $this->assertFalse(file_exists($file), "removes file.");

        $file = './test_dir/test.php';
        $filesystem->file($file);
        $this->assertTrue(file_exists($file), "creates directory of file if not exists.");
        $filesystem->remove($file);
        $filesystem->remove('./test_dir');
    }

    public function testDirCreation()
    {
        $filesystem = new Filesystem();

        $dir = './test_dir';
        $filesystem->dir($dir);
        $this->assertTrue(file_exists($dir));
        $filesystem->remove($dir);
        $this->assertFalse(file_exists($dir));

        $subDir = '/sub_dir';
        $filesystem->dir($dir . $subDir);
        $this->assertTrue(file_exists($dir), "when the are more than one level, creates parent dirs also.");
        $this->assertTrue(file_exists($dir . $subDir), "creates recursive dirs.");
        $filesystem->remove($dir);
        $this->assertFalse(file_exists($dir), "removes dir.");
        $this->assertFalse(file_exists($dir . $subDir), "removes dir and contents.");
    }

    public function testDirOperations()
    {
        $filesystem = new Filesystem();

        $dir = './test_dir_1';
        $filesystem->dir($dir);
        touch($dir . '/test.php');
        $filesystem->dir($dir . '/sub_dir');
        touch($dir. '/sub_dir/test.php');

        $destination = './test_dir_2';

        $filesystem->copy($dir, $destination);
        $this->assertTrue(file_exists($destination), "Creates destination dir if not exists.");
        $this->assertTrue(file_exists($destination . '/test.php'), "Copy dir contents to destination dir.");
        $this->assertTrue(file_exists($destination . '/sub_dir/test.php'), "Copy dir contents recursively to destination dir.");

        $filesystem->remove($dir);
        $filesystem->remove($destination);
    }
}
