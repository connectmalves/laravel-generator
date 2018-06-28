<?php

namespace InfyOm\Generator\Utils;

class FileUtil
{
    public static function createFile($path, $fileName, $contents)
    {
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $path = $path.$fileName;

        file_put_contents($path, $contents);
    }

    public static function createDirectoryIfNotExist($path, $replace = false)
    {
        if (file_exists($path) && $replace) {
            rmdir($path);
        }

        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
    }

    public static function deleteFile($path, $fileName)
    {
        if (file_exists($path.$fileName)) {
            return unlink($path.$fileName);
        }

        return false;
    }

    public static function insert_after_regex($pattern, $replacement, $subject)
    {
        preg_match($pattern, $subject, $matches, PREG_OFFSET_CAPTURE);
        if(!isset($matches[0][0]))
            return $subject;
        $concatenate = $matches[0][0];
        return preg_replace($pattern, $concatenate.$replacement, $subject);
    }
}
