<?php
use nstdio\FilePager;
use PHPUnit\Framework\TestCase;
require_once 'MockFile.php';

/**
 * Class PageSizeTest
 *
 * @author Edgar Asatryan <nstdio@gmail.com>
 */
class PageSizeTest extends TestCase
{
    private static $ltPageSize;
    private static $eqPageSize;
    private static $gtPageSize;
    private static $pageSize;

    public static function setUpBeforeClass()
    {
        self::$pageSize = 10;
        self::$ltPageSize = 'lt_page_size.txt';
        self::$eqPageSize = 'eq_page_size.txt';
        self::$gtPageSize = 'gt_page_size.txt';

        self::createFiles();
    }

    public static function tearDownAfterClass()
    {
        unlink(self::$ltPageSize);
        unlink(self::$eqPageSize);
        unlink(self::$gtPageSize);
    }

    private static function createFiles()
    {
        MockFile::create(self::$ltPageSize, abs(self::$pageSize - 5));
        MockFile::create(self::$eqPageSize, self::$pageSize);
        MockFile::create(self::$gtPageSize, self::$pageSize + 10);
    }

    public function testLessThenPageSize()
    {
        $fp = new FilePager(self::$ltPageSize, self::$pageSize);

        $page = $fp->getPage(1);

        $this->assertAttributeEquals(count(explode("\n", $page)), 'pageSize', $fp);
        $this->assertAttributeNotEquals(self::$pageSize, 'pageSize', $fp);
    }

    /**
     * @depends testLessThenPageSize
     */
    public function testEqualsPageSize()
    {
        $fp = new FilePager(self::$eqPageSize, self::$pageSize);

        $page = $fp->getPage(1);

        $this->assertAttributeEquals(count(explode("\n", $page)), 'pageSize', $fp);
        $this->assertAttributeEquals(self::$pageSize, 'pageSize', $fp);
    }

    /**
     * @depends testEqualsPageSize
     */
    public function testGatherPageSize()
    {
        $fp = new FilePager(self::$gtPageSize, self::$pageSize);

        $page = $fp->getPage(1);
        $fetched = count(explode("\n", $page));
        $this->assertAttributeEquals($fetched, 'pageSize', $fp, 'Fetched');
        $this->assertAttributeEquals(self::$pageSize, 'pageSize', $fp);
    }
}