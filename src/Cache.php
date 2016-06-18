<?php
namespace nstdio;
use RuntimeException;

/**
 * Class Cache
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class Cache
{
    /**
     * @var string
     */
    private $dir = __DIR__ . "/../cache";

    /**
     * @var string
     */
    private $ext = "bin";

    /**
     * @var string
     */
    private $cachedFileName;

    /**
     * @var CacheItem
     */
    private $item;

    /**
     * @var bool
     */
    private $exist;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $this->cachedFileName = $this->generateFileName();
    }

    private function generateFileName()
    {
        return sha1($this->fileName, false);
    }

    /**
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param mixed $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }

    public function create(CacheItem $item)
    {
        if (!File::isDir($this->dir) && File::makeDir($this->dir) === false) {
            throw new RuntimeException("Unable to create cache directory: {$this->dir}.");
        }
        return File::write($this->getPath(), serialize($item));
    }

    private function getPath()
    {
        return sprintf("%s/%s.%s", $this->dir, $this->cachedFileName, $this->ext);
    }

    public function exist()
    {
        return File::exists($this->getPath());
    }

    public function upToDate($pageSize)
    {
        $this->lazyLoad();
        return $this->item->getSrcModTime() === File::modTime($this->fileName) &&
        $pageSize === $this->item->getPageSize();
    }

    public function lazyLoad()
    {
        if ($this->item === null) {
            $this->load();
        }
    }

    public function load()
    {
        $this->item = unserialize(File::read($this->getPath()));
        if (!($this->item instanceof CacheItem)) {
            $this->item = null;
            throw new CorruptedDataException;
        }
    }

    public function get()
    {
        $this->lazyLoad();
        return $this->item;
    }

    public function isLoaded()
    {
        return $this->item !== null;
    }

    /**
     * @return string
     */
    public function getCachedFileName()
    {
        return $this->cachedFileName . "." . $this->ext;
    }
}