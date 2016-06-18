<?php
use nstdio\Cache;
use nstdio\FilePaginator;
use PHPUnit\Framework\TestCase;

/**
 * Class FileSystemTest
 *
 * @author Edgar Asatryan <nstdio@gmail.com>
 */
class FileSystemTest extends TestCase
{
    private $notExistingFile;
    private $notReadableFile;
    private $goodFile;

    /**
     * @var Cache
     */
    private $goodCache;

    public function setUp()
    {
        $this->goodFile = "C:\\Users\\Asatryan\\Desktop\\qconsole.log";
        $this->notExistingFile = "not_existing_file";
        $this->notReadableFile = "C:\\Users\\Asatryan\\Desktop\\New Text Document.txt";

        $this->goodCache = new Cache($this->goodFile);
    }

    public function tearDown()
    {
        self::delTree($this->goodCache->getDir());
    }

    public static function delTree($dir)
    {
        if (!is_dir($dir)) {
            return true;
        }
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? self::delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    /**
     * @test
     */
    public function noSuchFile()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("No such file: $this->notExistingFile.");

        new FilePaginator($this->notExistingFile);
    }

    /**
     * @test
     */
    public function notReadable()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage("No permission to read $this->notReadableFile file.");

        new FilePaginator($this->notReadableFile);
    }

    /**
     * @test
     */
    public function createCashDirAndFileWithSinglePage()
    {
        $fp = new FilePaginator($this->goodFile);
        $fp->setCache($this->goodCache);

        $fp->getPage(1);

        $cacheDir= $fp->getCache()->getDir();
        $file = $cacheDir . '/' . $fp->getCache()->getCachedFileName();

        $this->assertTrue(is_dir($cacheDir), "Cannot locate cache directory: $cacheDir");
        $this->assertTrue(is_file($file), "Cannot create cache file: $file");
        $this->assertTrue(is_readable($file), "Don't have permission to read file: $file");
        $this->assertTrue(is_writable($file), "Don't have permission to write in file: $file");
    }

    /**
     * @test
     */
    public function createCashDirAndFileWithRange()
    {
        $fp = new FilePaginator($this->goodFile);
        $fp->setCache($this->goodCache);

        $fp->getRange(1, 20);

        $cacheDir= $fp->getCache()->getDir();
        $file = $cacheDir . '/' . $fp->getCache()->getCachedFileName();

        $this->assertTrue(is_dir($cacheDir), "Cannot locate cache directory: $cacheDir");
        $this->assertTrue(is_file($file), "Cannot create cache file: $file");
        $this->assertTrue(is_readable($file), "Don't have permission to read file: $file");
        $this->assertTrue(is_writable($file), "Don't have permission to write in file: $file");
    }
}