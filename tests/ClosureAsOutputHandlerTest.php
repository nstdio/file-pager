<?php
require_once 'OutputTest.php';
/**
 * Class ClosureAsOutputHandlerTest
 *
 * @author Edgar Asatryan <nstdio@gmail.com>
 */
class ClosureAsOutputHandlerTest extends OutputTest
{

    public function testSetClosure()
    {
        $start = "<h1>";
        $end = "</h1>";
        $this->fp->setOutput(function ($line) use ($start, $end){
            return $start . rtrim($line) . $end;
        });

        $this->assertAttributeInstanceOf('Closure', 'output', $this->fp);
        $page = $this->fp->getPage(1);
        $exploded = explode("\n", $page);
        foreach ($exploded as $value) {
            $this->assertStringStartsWith($start, $value);
            $this->assertStringEndsWith($end, $value);
        }
    }
}