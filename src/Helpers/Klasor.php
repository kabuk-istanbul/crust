<?php

namespace Cruster\Helpers;

class Klasor
{
    public static function copyDirContents($fromDir, $toDir)
    {
        $dir = dir($fromDir);
        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }
            self::copy("$fromDir/$entry", "$toDir/$entry");
        }

        $dir->close();
        return true;
    }

    public static function copyTo($file, $dir)
    {
        $name = basename($file);
        self::copy($file, "$dir/$name");
    }

    public static function copy($source, $destination)
    {
        if (is_dir($source)) {
            self::mkdir($destination);
            return self::copyDirContents($source, $destination);
        }
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }
        return copy($source, $destination);
    }

    public static function mkdir($dir, $mode = 0777)
    {
        if (!file_exists($dir)) {
            mkdir($dir, $mode);
        }
    }
}