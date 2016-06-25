<?php
use nstdio\FilePager;
use PHPUnit\Framework\TestCase;

/**
 * Class OutputTest
 *
 * @author Edgar Asatryan <nstdio@gmail.com>
 */
class OutputTest extends TestCase
{
    protected $fileName;

    /**
     * @var FilePager
     */
    protected $fp;

    protected $pageSize;

    protected $page;

    public function setUp()
    {
        $this->fileName = "mock.txt";
        $this->pageSize = 50;
        $this->page = 1;
        try {
            $this->fp = new FilePager($this->fileName, $this->pageSize);
        } catch (InvalidArgumentException $ignored) {

        }
    }

    /**
     * @test
     */
    public function mockFileExist()
    {
        $this->assertFileExists($this->fileName, 'Place the mock.txt into tests directory.');
        $this->assertStringNotEqualsFile($this->fileName, '', 'mock.txt seems to be an empty file. Please fix it.');
    }

    /**
     * @test
     * @depends mockFileExist
     */
    public function pageSizeChange()
    {
        $page = $this->fp->getPage($this->page);
        $this->assertAttributeEquals(count(explode("\n", $page)), 'pageSize', $this->fp);

        $newPageSize = 10;
        $fp2 = new FilePager($this->fileName, $newPageSize);

        $newPage = $fp2->getPage($this->page);
        $cnt = count(explode("\n", $newPage));

        $this->assertTrue($fp2->getCache()->upToDate($newPageSize), 'Page size changed, but cache is valid!');
        $this->assertEquals($newPageSize, $fp2->getCache()->get()->getPageSize(), 'In cache item not valid page size.');

        $this->assertEquals($newPageSize, $cnt, "New page size is {$newPageSize}, but fetched lines count is {$cnt}");
    }

    /**
     * @test
     * @depends pageSizeChange
     */
    public function identicalContent()
    {
        $offset = $this->page === 1 ? 0 : ($this->page - 1) * $this->pageSize;
        $fileAsArray = array_slice(file($this->fileName), $offset, $this->pageSize);

        $page = $this->fp->getPage($this->page);
        $exploded = explode("\n", $page);

        $this->compare($fileAsArray, $exploded);
    }

    /**
     * @test
     * @depends identicalContent
     */
    public function prependAppendLineTest()
    {
        $prepend = "!!! PREPEND !!!";
        $append = "!!! APPEND !!!";
        $this->fp->prependLine($prepend);
        $this->fp->appendLine($append);
        $page = $this->fp->getPage($this->page);
        $exploded = explode("\n", $page);
        foreach ($exploded as $value) {
            $this->assertStringStartsWith($prepend, $value);
            $this->assertStringEndsWith($append, rtrim($value));
        }
    }


    /**
     * @param $fileAsArray
     * @param $exploded
     */
    private function compare($fileAsArray, $exploded)
    {
        foreach ($fileAsArray as $key => $value) {
            $line = $exploded[$key] . "\n"; // we add separator that explode trim.
            $this->assertEquals(0, strcmp($line, $value), "Actual: $exploded[$key], expect $value");
        }
    }
}