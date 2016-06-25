<?php
namespace nstdio;

/**
 * Interface OutputInterface
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
interface OutputInterface
{
    /**
     * @param string $line Reads a line in the file
     *
     * @return string
     */
    public function handle($line);

    /**
     * @param string|array $append    Appends a string to the end of the page.
     * @param bool         $useHandle If true [[$append]] will be passed into [[handle()]]
     *
     * @return mixed
     */
    public function append($append, $useHandle = false);

    /**
     * @param string|array $prepend   Prepends a string to the end of the page.
     * @param bool         $useHandle If true [[$string]] will be passed into [[handle()]]
     *
     * @return mixed
     */
    public function prepend($prepend, $useHandle = false);
}