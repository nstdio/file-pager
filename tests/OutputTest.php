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
    private $fileName;

    /**
     * @var FilePager
     */
    private $fp;

    private $pageSize;

    private $page;

    private $pageEnd;

    public function setUp()
    {
        $this->fileName = "mock.txt";
        $this->pageSize = 50;
        $this->page = 1;
        $this->pageEnd = $this->page + 5;
        $this->fp = new FilePager($this->fileName, $this->pageSize);
    }

    /**
     * @test
     */
    public function mockFileExist()
    {
        $this->assertFileExists($this->fileName, 'Place the mock.txt into tests directory.');
    }

    /**
     * @test
     * @depends mockFileExist
     */
    public function pageSizeChange()
    {
        $page = $this->fp->getPage($this->page);
        $this->assertEquals($this->pageSize, count(explode("\n", $page)) - 1);

        $newPageSize = 10;
        $fp2 = new FilePager($this->fileName, $newPageSize);

        $newPage = $fp2->getPage($this->page);
        $cnt = count(explode("\n", $newPage)) - 1;

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

        $this->fp->setOutput(function($line) {
            return $line;
        });
        $page = $this->fp->getPage($this->page);
        $exploded = explode("\n", $page);

        $this->compare($fileAsArray, $exploded);

        $page = $this->fp->getRange($this->page, $this->pageEnd);

        $exploded = explode("\n", $page);

        $this->compare($fileAsArray, $exploded);
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