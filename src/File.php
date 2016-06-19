<?php
namespace nstdio;

/**
 * Class File
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class File
{
    public static function isDir($dir)
    {
        return is_dir($dir);
    }

    public static function exists($fileName)
    {
        return is_file($fileName) && file_exists($fileName);
    }

    public static function write($fileName, $data)
    {
        $handle = fopen($fileName, 'w');
        if ($handle !== false) {
            fwrite($handle, $data);
            fclose($handle);
            return true;
        }

        return false;
    }

    /**
     * // TODO use fopen to read the file.
     *
     * @param $fileName
     *
     * @return string
     */
    public static function read($fileName)
    {
        return file_get_contents($fileName);
    }

    public static function modTime($fileName)
    {
        return filemtime($fileName);
    }

    public static function isWritable($fileName)
    {
        return is_writable($fileName);
    }

    public static function isReadable($fileName)
    {
        return is_readable($fileName);
    }

    public static function makeDir($dir)
    {
        return mkdir($dir, 0755, true);
    }

    public static function size($fileName)
    {
        return filesize($fileName);
    }
}