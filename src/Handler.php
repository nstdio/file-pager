<?php

namespace nstdio;

/**
 * Class Handler
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class Handler
{
    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var resource
     */
    protected $fileHandler;

    /**
     * @var
     */
    private $mod;

    /**
     * @inheritdoc
     */
    public function __construct($fileName)
    {
        $this->setFileName($fileName);
    }

    /**
     * @param string $fileName
     */
    private function setFileName($fileName)
    {
        if (is_file($fileName) && is_readable($fileName)) {
            $this->fileName = $fileName;
        } else {
            throw new \InvalidArgumentException("File does not exists or it's not readable: $fileName");
        }
    }

    /**
     * Open file descriptor
     */
    public function open()
    {
        if (!$this->isOpen()) {
            if ($this->mod === null) {
                $this->mod = 'r';
            }
            $this->fileHandler = fopen($this->fileName, $this->mod);
            flock($this->fileHandler, LOCK_SH);
        }
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @return resource
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }

    public function seek($where)
    {
        return fseek($this->fileHandler, $where);
    }

    public function tell()
    {
        return ftell($this->fileHandler);
    }

    /**
     *
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     *
     */
    public function close()
    {
        if ($this->isOpen()) {
            fclose($this->fileHandler);
        }
        unset($this->fileHandler);
    }

    public function isOpen()
    {
        return is_resource($this->fileHandler);
    }

    /**
     * @return mixed
     */
    public function getMod()
    {
        return $this->mod;
    }

    /**
     * @param mixed $mod
     */
    public function setMod($mod)
    {
        $this->mod = $mod;
    }

    /**
     * Sets file pointer to begin of the file.
     *
     * @return bool
     */
    protected function rewind()
    {
        return rewind($this->fileHandler);
    }

}