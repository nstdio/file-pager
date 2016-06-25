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
     * @param $line
     * @param $useHandle
     *
     * @return string
     */
    private function tryUseHandle($line, $useHandle)
    {
        if (is_array($line)) {
            $line = $this->handleArray($line);
        }
        if ($useHandle) {
            return $this->handle($line);
        }

        return $line;
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
    public function append($append, $useHandle = false)
    {
        return $this->tryUseHandle($append, $useHandle);
    }

    /**
     * @inheritdoc
     */
    public function prepend($prepend, $useHandle = false)
    {
        return $this->tryUseHandle($prepend, $useHandle);
    }
}
