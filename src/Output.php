<?php
namespace nstdio;

/**
 * Class Output
 *
 * @package nstdio
 * @author  Edgar Asatryan <nstdio@gmail.com>
 */
class Output implements OutputInterface
{
    /**
     * @inheritdoc
     */
    public function append($append, $useHandle = false)
    {
        if (is_array($append)) {
            $append = $this->handleArray($append);
        }
        return $this->tryUseHandle($append, $useHandle);
    }

    /**
     *
     * @param $array
     *
     * @return string
     */
    private function handleArray($array)
    {
        $sep = array_key_exists('sep', $array) ? $array['sep'] : " ";
        unset($array['sep']);
        $string = implode($sep, $array);
        return $string;
    }

    /**
     * @param $prepend
     * @param $useHandle
     *
     * @return string
     */
    private function tryUseHandle($prepend, $useHandle)
    {
        if ($useHandle) {
            return $this->handle($prepend);
        }
        return $prepend;
    }

    /**
     * @inheritdoc
     */
    public function handle($line)
    {
        return htmlspecialchars($line);
    }

    /**
     * @inheritdoc
     */
    public function prepend($prepend, $useHandle = false)
    {
        if (is_array($prepend)) {
            $prepend = $this->handleArray($prepend);
        }
        return $this->tryUseHandle($prepend, $useHandle);
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