<?php

namespace Crust\Helpers;

class Filesystem
{
    public function file($file)
    {
        if (!file_exists($file)) {
            $path = pathinfo($file);
            if (!empty($path['dirname'])) {
                $this->dir($path['dirname']);
            }
            return touch($file);
        }
        return true;
    }

    public function copyDir($fromDir, $toDir)
    {
        $this->dir($toDir);
        $dir = dir($fromDir);
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            $this->copy("$fromDir/$entry", "$toDir/$entry");
        }
        $dir->close();
        return true;
    }

    public function copyTo($file, $dir)
    {
        $name = basename($file);
        $this->copy($file, "$dir/$name");
    }

    public function copy($source, $destination)
    {
        if (is_dir($source)) {
            return $this->copyDir($source, $destination);
        }
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }
        return copy($source, $destination);
    }

    public function remove($file)
    {
        if (file_exists($file)) {
            if (is_dir($file)) {
                return $this->rmdir($file);
            }
            else {
                return unlink($file);
            }
        }
        return true;
    }

    public function dir($dir, $mode = 0777, $recursive = true)
    {
        if (!file_exists($dir)) {
            return mkdir($dir, $mode, $recursive);
        }
        return true;
    }

    public function rmdir($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->rmdir("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }
}