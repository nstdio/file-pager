<?php
namespace nstdio;

use Closure;
use InvalidArgumentException;

/**
 * Class FilePager
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class FilePager implements OutputInterface
{
    /**
     * @var int
     */
    private $pageSize;

    /**
     * @var Handler
     */
    private $handler;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var string
     */
    private $append;

    /**
     * @var string
     */
    private $prepend;

    /**
     * @var string
     */
    private $appendLine;

    /**
     * @var
     */
    private $prependLine;

    /**
     * @var int
     */
    private $lineNumber = 1;

    /**
     * @var int
     */
    private $realLineNumber;

    /**
     * @var int
     */
    private $page;

    /**
     * @var string
     */
    private $lineSeparator = LineSeparator::AUTO;

    /**
     * FilePaginator constructor.
     *
     * @param string $fileName
     * @param int    $pageSize
     */
    public function __construct($fileName, $pageSize = 50)
    {
        $this->checkFile($fileName);
        $this->handler = new Handler($fileName);
        $this->cache = new Cache($fileName);
        $this->pageSize = $pageSize;
    }

    private function checkFile($fileName)
    {
        if (!File::exists($fileName)) {
            throw new InvalidArgumentException("No such file: $fileName.");
        }
        if (!File::isReadable($fileName)) {
            throw new InvalidArgumentException("No permission to read $fileName file.");
        }
    }

    /**
     * @param $page
     *
     * @return string
     */
    public function getPage($page)
    {
        $this->checkCache();

        $this->page = (int)$page;
        if ($this->page <= 0) {
            $this->page = 1;
        }
        if ($this->page > $this->cache->get()->getPageCount()) {
            $this->page = $this->cache->get()->getPageCount();
        }
        return $this->read();
    }

    private function checkCache()
    {
        $this->handler->open();
        if ($this->isCached()) {
            $this->updateIfNeeded();
        } else {
            $this->createCache();
        }
    }

    /**
     * @return bool Whether file cache exists or not.
     */
    private function isCached()
    {
        if ($this->cache->exist()) {
            try {
                $this->cache->lazyLoad();
            } catch (CorruptedDataException $e) {
                $this->createCache();
            }

            return true;
        }

        return false;
    }

    private function createCache()
    {
        $lineNumber = 1;
        $pos = [0];
        $handler = $this->handler->getFileHandler();
        while (fgets($handler, 8196) !== false) {
            if ($lineNumber % $this->pageSize === 0) {
                $pos[] = ftell($handler);
            }
            $lineNumber++;
        }
        if (count($pos) === 1) {
            $this->pageSize = $lineNumber - 1;
        }
        array_push($pos, File::size($this->getFileName()));
        $item = new CacheItem($pos, File::modTime($this->getFileName()), $this->pageSize);
        $this->cache->create($item);
    }

    private function getFileName()
    {
        return $this->handler->getFileName();
    }

    private function updateIfNeeded()
    {
        if (!$this->cache->upToDate($this->pageSize)) {
            $this->createCache();
            $this->cache->load();
        }
    }

    /**
     *
     * @return string
     */
    private function read()
    {
        $offset = $this->cache->get()->get($this->page);

        return $this->readOffset($offset[0], $offset[1]);
    }

    /**
     * @param $start
     *
     * @param $end
     *
     * @return string
     */
    private function readOffset($start, $end)
    {
        $ret = '';
        $this->initRealLineNumber();
        $this->handler->seek($start);
        if ($this->prepend !== null) {
            $ret .= $this->prepend;
        }
        while (($line = fgets($this->handler->getFileHandler(), 8196)) !== false) {
            if ($this->handler->tell() > $end) {
                return rtrim($ret);
            }

            if ($this->prependLine !== null) {
                $line = $this->replaceToken($this->prependLine) . $line;
            }
            $ret .= $this->handle($line);
            if ($this->appendLine !== null) {
                $ret = rtrim($ret) . $this->replaceToken($this->appendLine) . $this->lineSeparator;
            }
            if ($this->lineNumber % $this->pageSize === 0) {
                $ret = $this->concatPage($ret);
            }
            $this->realLineNumber++;
            $this->lineNumber++;
            $ret .= $this->lineSeparator;
        }
        if ($this->page === $this->cache->get()->getPageCount()) {
            $ret = $this->concatPage($ret);
        }
        $this->lineNumber = 1;

        return rtrim($ret);
    }

    private function replaceToken($prependLine)
    {
        $replace = [
            '{line}'     => $this->realLineNumber,
            '{pageLine}' => $this->lineNumber,
            '{path}'     => $this->getFileName(),
            '{file}'     => basename($this->getFileName()),
            '{dir}'      => dirname($this->getFileName()),
            '{page}'     => $this->page,
        ];

        return strtr($prependLine, $replace);
    }

    /**
     * @inheritdoc
     */
    public function handle($line)
    {
        $this->lazyOutputInit();
        if ($this->output instanceof OutputInterface) {
            return $this->output->handle($line);
        }
        if ($this->output instanceof Closure) {
            return $this->output->__invoke($line);
        }

        return $line;
    }

    private function lazyOutputInit()
    {
        if ($this->output === null) {
            $this->output = new Output();
        }
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface | Closure $output
     */
    public function setOutput($output)
    {
        if (!($output instanceof OutputInterface) && !($output instanceof Closure)) {
            $output = new Output();
        }
        $this->output = $output;
    }

    /**
     * @inheritdoc
     */
    public function append($append, $useHandle = false)
    {
        $this->lazyOutputInit();
        if ($this->output instanceof Closure && $useHandle) {
            $ret = $this->output->__invoke($append);
        } else {
            $ret = $this->output->append($append, $useHandle);
        }

        $this->append = $this->append === null ? $ret : $this->append .= $ret;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function prepend($prepend, $useHandle = false)
    {
        $this->lazyOutputInit();
        if ($this->output instanceof Closure && $useHandle) {
            $ret = $this->output->__invoke($prepend);
        } else {
            $ret = $this->output->prepend($prepend, $useHandle);
        }

        $this->prepend = $this->prepend === null ? $ret : $this->prepend .= $ret;

        return $this;
    }

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @inheritdoc
     */
    public function appendLine($append, $useHandle = false)
    {
        $this->lazyOutputInit();
        if ($this->output instanceof Closure && $useHandle) {
            $ret = $this->output->__invoke($append);
        } else {
            $ret = $this->output->append($append, $useHandle);
        }

        $this->appendLine = $this->appendLine === null ? $ret : $this->appendLine .= $ret;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function prependLine($prepend, $useHandle = false)
    {
        $this->lazyOutputInit();
        if ($this->output instanceof Closure && $useHandle) {
            $ret = $this->output->__invoke($prepend);
        } else {
            $ret = $this->output->prepend($prepend, $useHandle);
        }

        $this->prependLine = $this->prependLine === null ? $ret : $this->prependLine .= $ret;

        return $this;
    }

    private function initRealLineNumber()
    {
        $this->realLineNumber = $this->page === 1 ? $this->lineNumber : ($this->lineNumber + $this->pageSize) * ($this->page - 1);
        if ($this->page > 2) {
            $this->realLineNumber = $this->realLineNumber - $this->page + 2;
        }
    }

    /**
     * @param $ret
     *
     * @return string
     */
    private function concatPage($ret)
    {
        if ($this->append !== null) {
            $ret .= $this->lineSeparator . $this->replaceToken($this->append);
        }
        if ($this->prepend !== null) {
            $ret .= $this->lineSeparator . $this->replaceToken($this->prepend);
        }

        return $ret;
    }

    /**
     * @return string
     */
    public function getLineSeparator()
    {
        return $this->lineSeparator;
    }

    /**
     * @param string $lineSeparator Will be appended to the end of the line.
     *
     * @return $this
     */
    public function setLineSeparator($lineSeparator)
    {
        switch ($lineSeparator) {
            case LineSeparator::UNIX: break;
            case LineSeparator::WINDOWS: break;
            case LineSeparator::MAC: break;
            case LineSeparator::HTML: break;
            case LineSeparator::AUTO: break;
            default:
                throw new InvalidArgumentException("line separator must be one of LineSeparator constants. To append string to line please use FilePager::appendLine.");
                break;
        }

        $this->lineSeparator = $lineSeparator;

        return $this;
    }
}