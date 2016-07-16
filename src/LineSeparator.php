<?php
namespace nstdio;

/**
 * Class LineSeparator
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class LineSeparator
{
    const UNIX = "\n";

    const WINDOWS = "\r\n";

    const MAC = "\r";

    const HTML = "<br>";

    const AUTO = PHP_EOL;
}