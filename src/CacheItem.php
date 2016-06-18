<?php
namespace nstdio;

/**
 * Class CacheItem
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class CacheItem
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var int
     */
    private $srcModTime;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * CacheItem constructor.
     *
     * @param array $data
     * @param       $modTime
     * @param       $pageSize
     */
    public function __construct(array $data, $modTime, $pageSize)
    {
        $this->data = $data;
        $this->srcModTime = $modTime;
        $this->pageSize = $pageSize;
    }

    /**
     * @return mixed
     */
    public function getSrcModTime()
    {
        return $this->srcModTime;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $page
     *
     * @return array
     */
    public function get($page)
    {
        return [
            $this->data[$page - 1],
            $this->data[$page],
        ];
    }
}