<?php
namespace nstdio;

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
     * @var int
     */
    private $lineNumber = 1;

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
        $page = (int)$page;
        if ($page <= 0) {
            $page = 1;
        }
        $this->checkCache();

        return $this->read($page);
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
        $item = new CacheItem($pos, File::modTime($this->getFileName()), $this->pageSize);
        $this->cache->create($item);
    }

    public function getFileName()
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
     * @param int $page
     *
     * @return string
     */
    private function read($page)
    {
        $offset = $this->cache->get()->get($page);

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
        $this->handler->seek($start);
        while (($line = fgets($this->handler->getFileHandler(), 8196)) !== false) {
            if ($this->handler->tell() > $end) {
                return $ret;
            }

            $ret .= $this->handle($line);
            if ($this->lineNumber % $this->pageSize === 0) {
                if ($this->prepend !== null) {
                    $ret .= $this->prepend;
                }
                if ($this->append !== null) {
                    $ret .= $this->append;
                }
            }
            $this->lineNumber++;
        }
        $this->lineNumber = 1;

        return $ret;
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

        return $line;
    }

    private function lazyOutputInit()
    {
        if ($this->output === null) {
            $this->output = new Output();
        }
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        if (!($output instanceof OutputInterface)) {
            $output = new Output();
        }
        $this->output = $output;
    }

    /**
     * @param $start
     * @param $end
     *
     * @return string
     */
    public function getRange($start, $end)
    {
        if ($end < $start) {
            $start = $end;
        }
        $this->checkCache();

        $start = $this->cache->get()->get($start)[0];
        $end = $this->cache->get()->get($end)[1];

        return $this->readOffset($start, $end);
    }

    /**
     * @inheritdoc
     */
    public function append($append, $useHandle = false)
    {
        $this->lazyOutputInit();
        $ret = $this->output->append($append, $useHandle);

        $this->append = $this->append === null ? $ret : $this->append .= $ret;
    }

    /**
     * @inheritdoc
     */
    public function prepend($prepend, $useHandle = false)
    {
        $this->lazyOutputInit();
        $ret = $this->output->prepend($prepend, $useHandle);
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
    public function appendLine($append, $userHandle = false)
    {
        // TODO: Implement appendLine() method.
    }

    /**
     * @inheritdoc
     */
    public function prependLine($prepend, $userHandle = false)
    {
        // TODO: Implement prependLine() method.
    }
}